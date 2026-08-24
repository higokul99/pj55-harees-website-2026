<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PhonePeService;
use App\Models\PhonePeOrder;
use App\Models\PhonePeWebhookLog;
use App\Models\PhonePeCallbackEvent;
use App\Models\CustomerOrder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PhonePeController extends Controller
{
    protected $phonePeService;

    public function __construct()
    {
        $this->phonePeService = PhonePeService::getInstance();
    }

    /**
     * Initiate PhonePe payment.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function initiatePayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:customer_orders,id',
                'amount' => 'required|integer|min:100',
            ]);

            $customerOrder = CustomerOrder::findOrFail($validated['order_id']);

            // Check if order already has a merchant_order_id
            if (empty($customerOrder->merchant_order_id)) {
                $merchantOrderId = PhonePeService::generateMerchantOrderId();
                $customerOrder->update([
                    'merchant_order_id' => $merchantOrderId,
                    'payment_method' => 'phonepe',
                ]);
            } else {
                $merchantOrderId = $customerOrder->merchant_order_id;
            }

            // Initiate payment
            $result = $this->phonePeService->initiatePayment(
                $merchantOrderId,
                $validated['amount'],
                config('phonepe.redirect_url'),
                "Payment for Order #{$customerOrder->id}"
            );

            if ($result['success']) {
                // Update customer order with PhonePe details
                $customerOrder->update([
                    'phonepe_order_id' => $result['order_id'],
                ]);

                return response()->json([
                    'success' => true,
                    'redirect_url' => $result['redirect_url'],
                    'order_id' => $result['order_id'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Payment initiation failed',
            ], 400);
        } catch (\Exception $e) {
            Log::error('Payment initiation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.',
            ], 500);
        }
    }

    /**
     * Handle PhonePe webhook callback.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handleCallback(Request $request)
    {
        try {
            // Get authorization header
            $authHeader = $request->header('Authorization');
            if (!$authHeader) {
                Log::warning('PhonePe webhook received without Authorization header');
                return response('Unauthorized', 401);
            }

            // Get raw request body
            $responseBody = $request->getContent();

            // Verify callback
            $verificationResult = $this->phonePeService->verifyCallback($authHeader, $responseBody);

            if (!$verificationResult['success'] || !$verificationResult['verified']) {
                Log::error('PhonePe webhook verification failed');
                return response('Verification failed', 400);
            }

            $payload = $verificationResult['payload'];
            $webhookLogId = $verificationResult['webhook_log_id'];

            // Process webhook based on event type
            $this->processWebhookEvent($payload, $webhookLogId);

            // Mark webhook as processed
            PhonePeWebhookLog::find($webhookLogId)->markAsProcessed();

            // Return 200 to acknowledge receipt
            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
            ]);

            // Return 500 so PhonePe will retry
            return response('Server error', 500);
        }
    }

    /**
     * Process webhook event.
     *
     * @param array $payload
     * @param int $webhookLogId
     */
    protected function processWebhookEvent(array $payload, int $webhookLogId): void
    {
        $event = $payload['event'] ?? null;
        $data = $payload['payload'] ?? [];

        // Store callback event
        PhonePeCallbackEvent::create([
            'event' => $event,
            'merchant_order_id' => $data['merchantOrderId'] ?? null,
            'merchant_refund_id' => $data['merchantRefundId'] ?? null,
            'state' => $data['state'] ?? null,
            'amount' => $data['amount'] ?? null,
            'occurred_at' => $data['timestamp'] ?? now()->timestamp * 1000,
        ]);

        // Process order-related events
        if (in_array($event, ['CHECKOUT_ORDER_COMPLETED', 'CHECKOUT_ORDER_FAILED'])) {
            $this->processOrderEvent($data, $event);
        }

        // Process refund-related events
        if (in_array($event, ['PG_REFUND_COMPLETED', 'PG_REFUND_FAILED', 'PG_REFUND_ACCEPTED'])) {
            $this->processRefundEvent($data, $event);
        }
    }

    /**
     * Process order event from webhook.
     *
     * @param array $data
     * @param string $event
     */
    protected function processOrderEvent(array $data, string $event): void
    {
        $merchantOrderId = $data['merchantOrderId'] ?? null;
        if (!$merchantOrderId) {
            return;
        }

        DB::transaction(function () use ($merchantOrderId, $data, $event) {
            // Update PhonePe order
            $phonepeOrder = PhonePeOrder::where('merchant_order_id', $merchantOrderId)->first();
            if ($phonepeOrder) {
                $phonepeOrder->update([
                    'state' => $data['state'],
                    'last_known_state' => $data['state'],
                ]);
            }

            // Update customer order
            $customerOrder = CustomerOrder::where('merchant_order_id', $merchantOrderId)->first();
            if ($customerOrder) {
                $paymentStatus = ($event === 'CHECKOUT_ORDER_COMPLETED') ? 'completed' : 'failed';
                $orderStatus = ($event === 'CHECKOUT_ORDER_COMPLETED') ? 'confirmed' : 'failed';

                $customerOrder->update([
                    'payment_status' => $paymentStatus,
                    'status' => $orderStatus,
                    'phonepe_transaction_id' => $data['transactionId'] ?? null,
                ]);

                Log::info('Customer order updated via webhook', [
                    'order_id' => $customerOrder->id,
                    'merchant_order_id' => $merchantOrderId,
                    'payment_status' => $paymentStatus,
                ]);
            }
        });
    }

    /**
     * Process refund event from webhook.
     *
     * @param array $data
     * @param string $event
     */
    protected function processRefundEvent(array $data, string $event): void
    {
        // Implementation for refund webhook processing
        $merchantRefundId = $data['merchantRefundId'] ?? null;
        if (!$merchantRefundId) {
            return;
        }

        Log::info('Refund event received', [
            'event' => $event,
            'merchant_refund_id' => $merchantRefundId,
            'state' => $data['state'] ?? null,
        ]);

        // Update refund status if needed
        // This can be expanded based on business requirements
    }

    /**
     * Check payment status.
     *
     * @param string $merchantOrderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus(string $merchantOrderId)
    {
        try {
            $result = $this->phonePeService->checkOrderStatus($merchantOrderId);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'state' => $result['state'],
                    'amount' => $result['amount'],
                    'transaction_id' => $result['transaction_id'] ?? null,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to check order status',
            ], 400);
        } catch (\Exception $e) {
            Log::error('Status check error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }

    /**
     * Handle redirect from PhonePe after payment.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function handleRedirect(Request $request)
    {
        $merchantOrderId = $request->query('merchantOrderId');

        if (!$merchantOrderId) {
            return view('user.payment-status', [
                'status' => 'error',
                'message' => 'Invalid payment session',
            ]);
        }

        // Check order status
        $result = $this->phonePeService->checkOrderStatus($merchantOrderId);

        $customerOrder = CustomerOrder::where('merchant_order_id', $merchantOrderId)->first();

        if ($result['success']) {
            $status = match ($result['state']) {
                'COMPLETED' => 'success',
                'FAILED' => 'failed',
                'PENDING' => 'pending',
                default => 'unknown'
            };

            return view('user.payment-status', [
                'status' => $status,
                'state' => $result['state'],
                'order' => $customerOrder,
                'transaction_id' => $result['transaction_id'] ?? null,
                'amount' => $result['amount'],
            ]);
        }

        return view('user.payment-status', [
            'status' => 'error',
            'message' => 'Unable to verify payment status',
            'order' => $customerOrder,
        ]);
    }

    /**
     * Initiate refund.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function initiateRefund(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:customer_orders,id',
                'amount' => 'required|integer|min:100',
                'reason' => 'nullable|string',
            ]);

            $customerOrder = CustomerOrder::findOrFail($validated['order_id']);

            if (empty($customerOrder->merchant_order_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found or not paid via PhonePe',
                ], 400);
            }

            $merchantRefundId = PhonePeService::generateMerchantRefundId();

            $result = $this->phonePeService->initiateRefund(
                $merchantRefundId,
                $customerOrder->merchant_order_id,
                $validated['amount']
            );

            if ($result['success']) {
                // Update customer order
                $customerOrder->update(['payment_status' => 'refunded']);

                return response()->json([
                    'success' => true,
                    'refund_id' => $result['refund_id'],
                    'state' => $result['state'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Refund initiation failed',
            ], 400);
        } catch (\Exception $e) {
            Log::error('Refund initiation error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }

    /**
     * Check refund status.
     *
     * @param string $merchantRefundId
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkRefundStatus(string $merchantRefundId)
    {
        try {
            $result = $this->phonePeService->checkRefundStatus($merchantRefundId);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'state' => $result['state'],
                    'amount' => $result['amount'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to check refund status',
            ], 400);
        } catch (\Exception $e) {
            Log::error('Refund status check error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }
}
