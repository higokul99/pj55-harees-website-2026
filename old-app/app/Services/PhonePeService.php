<?php

namespace App\Services;

use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;
use PhonePe\payments\v2\models\request\builders\StandardCheckoutPayRequestBuilder;
use PhonePe\payments\v2\models\request\builders\StandardCheckoutRefundRequestBuilder;
use PhonePe\Env;
use PhonePe\common\exceptions\PhonePeException;
use App\Models\PhonePeOrder;
use App\Models\PhonePePaymentAttempt;
use App\Models\PhonePeOrderStatusCheck;
use App\Models\PhonePeRefund;
use App\Models\PhonePeRefundStatusCheck;
use App\Models\PhonePeWebhookLog;
use App\Models\PhonePeSecurityAudit;
use Illuminate\Support\Facades\Log;

class PhonePeService
{
    protected static $instance = null;
    protected $client;

    /**
     * Private constructor for singleton pattern.
     */
    private function __construct()
    {
        $this->initializeClient();
    }

    /**
     * Get singleton instance of PhonePeService.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialize PhonePe SDK client.
     */
    private function initializeClient(): void
    {
        try {
            $clientId = trim(config('phonepe.client_id'));
            $clientVersion = trim(config('phonepe.client_version'));
            $clientSecret = trim(config('phonepe.client_secret'));

            // Validate credentials
            if (empty($clientId) || empty($clientSecret) || empty($clientVersion)) {
                throw new \Exception('PhonePe credentials not configured. Please check .env file.');
            }

            $env = strtoupper(trim(config('phonepe.environment'))) === 'PRODUCTION'
                ? Env::PRODUCTION
                : Env::UAT;

            $shouldPublishEvents = config('phonepe.enable_logging', false);

            $this->client = StandardCheckoutClient::getInstance(
                $clientId,
                $clientVersion,
                $clientSecret,
                $env,
                $shouldPublishEvents
            );

            Log::info('PhonePe SDK client initialized successfully', [
                'environment' => config('phonepe.environment'),
                'client_id' => $clientId,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to initialize PhonePe SDK client', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get the PhonePe SDK client instance.
     */
    public function getClient(): StandardCheckoutClient
    {
        return $this->client;
    }

    /**
     * Initiate a payment.
     *
     * @param string $merchantOrderId Unique merchant order ID
     * @param int $amount Amount in paisa
     * @param string|null $redirectUrl URL to redirect after payment
     * @param string|null $message Optional message
     * @param array|null $metaInfo Optional metadata
     * @return array Payment response with redirect URL
     */
    public function initiatePayment(
        string $merchantOrderId,
        int $amount,
        ?string $redirectUrl = null,
        ?string $message = null,
        ?array $metaInfo = null
    ): array {
        try {
            // Validate amount
            if ($amount < config('phonepe.payment.min_amount', 100)) {
                throw new \Exception('Amount must be at least ₹1.00 (100 paisa)');
            }

            // Use default redirect URL if not provided
            $redirectUrl = $redirectUrl ?? config('phonepe.redirect_url');

            // Build payment request
            $requestBuilder = StandardCheckoutPayRequestBuilder::builder()
                ->merchantOrderId($merchantOrderId)
                ->amount($amount);

            if ($redirectUrl) {
                $requestBuilder->redirectUrl($redirectUrl);
            }

            if ($message) {
                $requestBuilder->message($message);
            }

            // Add meta info if provided
            if ($metaInfo) {
                // PhonePe supports udf1-udf5
                foreach (['udf1', 'udf2', 'udf3', 'udf4', 'udf5'] as $key) {
                    if (isset($metaInfo[$key])) {
                        $requestBuilder->$key($metaInfo[$key]);
                    }
                }
            }

            $payRequest = $requestBuilder->build();

            // Call PhonePe API
            $payResponse = $this->client->pay($payRequest);

            // Store payment initiation in database
            $phonepeOrder = PhonePeOrder::create([
                'merchant_order_id' => $merchantOrderId,
                'phonepe_order_id' => $payResponse->getOrderId(),
                'amount' => $amount,
                'currency' => 'INR',
                'state' => $payResponse->getState(),
                'last_known_state' => $payResponse->getState(),
                'expire_at' => $payResponse->getExpireAt(),
                'redirect_url' => $payResponse->getRedirectUrl(),
            ]);

            Log::info('PhonePe payment initiated', [
                'merchant_order_id' => $merchantOrderId,
                'phonepe_order_id' => $payResponse->getOrderId(),
                'amount' => $amount,
                'state' => $payResponse->getState(),
            ]);

            return [
                'success' => true,
                'state' => $payResponse->getState(),
                'redirect_url' => $payResponse->getRedirectUrl(),
                'order_id' => $payResponse->getOrderId(),
                'expire_at' => $payResponse->getExpireAt(),
            ];
        } catch (PhonePeException $e) {
            Log::error('PhonePe payment initiation failed', [
                'merchant_order_id' => $merchantOrderId,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'http_status' => $e->getHttpStatusCode(),
            ]);

            return [
                'success' => false,
                'error' => 'Payment initiation failed. Please try again.',
                'code' => $e->getCode(),
            ];
        }
    }

    /**
     * Check order status.
     *
     * @param string $merchantOrderId Merchant order ID
     * @param bool $detailsFlag Whether to fetch all attempts or just latest
     * @return array Order status response
     */
    public function checkOrderStatus(string $merchantOrderId, bool $detailsFlag = true): array
    {
        try {
            $statusResponse = $this->client->getOrderStatus($merchantOrderId, $detailsFlag);

            // Store status check in database
            PhonePeOrderStatusCheck::create([
                'merchant_order_id' => $merchantOrderId,
                'phonepe_order_id' => $statusResponse->getOrderId(),
                'state' => $statusResponse->getState(),
                'amount' => $statusResponse->getAmount(),
                'response' => json_decode(json_encode($statusResponse), true),
            ]);

            // Update PhonePe order if exists
            $phonepeOrder = PhonePeOrder::where('merchant_order_id', $merchantOrderId)->first();
            if ($phonepeOrder) {
                $phonepeOrder->update([
                    'state' => $statusResponse->getState(),
                    'last_known_state' => $statusResponse->getState(),
                ]);
            }

            // Store payment attempts
            if ($statusResponse->getPaymentDetails()) {
                foreach ($statusResponse->getPaymentDetails() as $attempt) {
                    PhonePePaymentAttempt::updateOrCreate(
                        ['transaction_id' => $attempt->getTransactionId()],
                        [
                            'merchant_order_id' => $merchantOrderId,
                            'payment_mode' => $attempt->getPaymentMode(),
                            'state' => $attempt->getState(),
                            'amount' => $attempt->getAmount(),
                            'timestamp' => $attempt->getTimestamp(),
                            'error_code' => $attempt->getErrorCode(),
                            'detailed_error_code' => $attempt->getDetailedErrorCode(),
                            'raw_response' => json_decode(json_encode($attempt), true),
                        ]
                    );
                }
            }

            Log::info('PhonePe order status checked', [
                'merchant_order_id' => $merchantOrderId,
                'state' => $statusResponse->getState(),
            ]);

            return [
                'success' => true,
                'order_id' => $statusResponse->getOrderId(),
                'merchant_order_id' => $merchantOrderId,
                'state' => $statusResponse->getState(),
                'amount' => $statusResponse->getAmount(),
                'transaction_id' => $statusResponse->getTransactionId(),
                'payment_details' => $statusResponse->getPaymentDetails(),
                'error_code' => $statusResponse->getErrorCode(),
                'detailed_error_code' => $statusResponse->getDetailedErrorCode(),
            ];
        } catch (PhonePeException $e) {
            Log::error('PhonePe order status check failed', [
                'merchant_order_id' => $merchantOrderId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to check order status.',
            ];
        }
    }

    /**
     * Initiate a refund.
     *
     * @param string $merchantRefundId Unique merchant refund ID
     * @param string $originalMerchantOrderId Original order ID
     * @param int $amount Refund amount in paisa
     * @return array Refund response
     */
    public function initiateRefund(
        string $merchantRefundId,
        string $originalMerchantOrderId,
        int $amount
    ): array {
        try {
            // Build refund request
            $refundRequest = StandardCheckoutRefundRequestBuilder::builder()
                ->merchantRefundId($merchantRefundId)
                ->originalMerchantOrderId($originalMerchantOrderId)
                ->amount($amount)
                ->build();

            // Call PhonePe API
            $refundResponse = $this->client->refund($refundRequest);

            // Store refund initiation
            PhonePeRefund::create([
                'merchant_refund_id' => $merchantRefundId,
                'merchant_order_id' => $originalMerchantOrderId,
                'phonepe_refund_id' => $refundResponse->getRefundId(),
                'amount' => $amount,
                'state' => $refundResponse->getState(),
            ]);

            Log::info('PhonePe refund initiated', [
                'merchant_refund_id' => $merchantRefundId,
                'original_order_id' => $originalMerchantOrderId,
                'amount' => $amount,
                'state' => $refundResponse->getState(),
            ]);

            return [
                'success' => true,
                'refund_id' => $refundResponse->getRefundId(),
                'amount' => $refundResponse->getAmount(),
                'state' => $refundResponse->getState(),
            ];
        } catch (PhonePeException $e) {
            Log::error('PhonePe refund initiation failed', [
                'merchant_refund_id' => $merchantRefundId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Refund initiation failed.',
            ];
        }
    }

    /**
     * Check refund status.
     *
     * @param string $merchantRefundId Merchant refund ID
     * @return array Refund status response
     */
    public function checkRefundStatus(string $merchantRefundId): array
    {
        try {
            $refundStatusResponse = $this->client->getRefundStatus($merchantRefundId);

            // Store refund status check
            PhonePeRefundStatusCheck::create([
                'merchant_refund_id' => $merchantRefundId,
                'state' => $refundStatusResponse->getState(),
                'response' => json_decode(json_encode($refundStatusResponse), true),
            ]);

            // Update refund if exists
            $refund = PhonePeRefund::where('merchant_refund_id', $merchantRefundId)->first();
            if ($refund) {
                $refund->update(['state' => $refundStatusResponse->getState()]);
            }

            Log::info('PhonePe refund status checked', [
                'merchant_refund_id' => $merchantRefundId,
                'state' => $refundStatusResponse->getState(),
            ]);

            return [
                'success' => true,
                'refund_id' => $refundStatusResponse->getRefundId(),
                'state' => $refundStatusResponse->getState(),
                'amount' => $refundStatusResponse->getAmount(),
            ];
        } catch (PhonePeException $e) {
            Log::error('PhonePe refund status check failed', [
                'merchant_refund_id' => $merchantRefundId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to check refund status.',
            ];
        }
    }

    /**
     * Verify webhook callback.
     *
     * @param string $authorizationHeader Authorization header from webhook
     * @param string $responseBody Raw response body from webhook
     * @return array Verification result with callback data
     */
    public function verifyCallback(string $authorizationHeader, string $responseBody): array
    {
        try {
            $username = config('phonepe.webhook_username');
            $password = config('phonepe.webhook_password');

            // Compute expected authorization
            $expectedAuth = 'Basic ' . base64_encode($username . ':' . $password);

            // Verify authorization header
            $verified = hash_equals($expectedAuth, $authorizationHeader);

            // Parse response body
            $payload = json_decode($responseBody, true);

            // Log webhook receipt
            $webhookLog = PhonePeWebhookLog::create([
                'event_type' => $payload['event'] ?? null,
                'merchant_order_id' => $payload['payload']['merchantOrderId'] ?? null,
                'phonepe_order_id' => $payload['payload']['orderId'] ?? null,
                'merchant_refund_id' => $payload['payload']['merchantRefundId'] ?? null,
                'authorization_header' => $authorizationHeader,
                'verified' => $verified,
                'payload' => $payload,
                'processing_status' => $verified ? 'RECEIVED' : 'FAILED',
                'failure_reason' => $verified ? null : 'Authorization verification failed',
            ]);

            // Log security audit
            PhonePeSecurityAudit::logWebhook(
                $payload['payload']['merchantOrderId'] ?? $payload['payload']['merchantRefundId'] ?? 'unknown',
                $authorizationHeader,
                $expectedAuth,
                $verified,
                $verified ? 'Webhook verified successfully' : 'Authorization mismatch'
            );

            if (!$verified) {
                Log::error('PhonePe webhook verification failed', [
                    'authorization_received' => $authorizationHeader,
                    'authorization_expected' => $expectedAuth,
                ]);

                return [
                    'success' => false,
                    'verified' => false,
                    'error' => 'Webhook verification failed',
                ];
            }

            Log::info('PhonePe webhook verified successfully', [
                'event' => $payload['event'] ?? null,
                'merchant_order_id' => $payload['payload']['merchantOrderId'] ?? null,
            ]);

            return [
                'success' => true,
                'verified' => true,
                'webhook_log_id' => $webhookLog->id,
                'payload' => $payload,
            ];
        } catch (\Exception $e) {
            Log::error('PhonePe webhook verification exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'verified' => false,
                'error' => 'Webhook verification failed',
            ];
        }
    }

    /**
     * Generate unique merchant order ID.
     *
     * @param string $prefix Optional prefix
     * @return string Unique merchant order ID
     */
    public static function generateMerchantOrderId(string $prefix = 'ORD'): string
    {
        return $prefix . '_' . time() . '_' . uniqid();
    }

    /**
     * Generate unique merchant refund ID.
     *
     * @param string $prefix Optional prefix
     * @return string Unique merchant refund ID  
     */
    public static function generateMerchantRefundId(string $prefix = 'REF'): string
    {
        return $prefix . '_' . time() . '_' . uniqid();
    }
}
