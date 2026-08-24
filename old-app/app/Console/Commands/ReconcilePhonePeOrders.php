<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReconcilePhonePeOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phonepe:reconcile {minutes=60 : Look back minutes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconcile pending PhonePe orders by checking status with gateway';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $minutes = $this->argument('minutes');
        $this->info("Starting reconciliation for orders in last {$minutes} minutes...");

        $phonePeService = app(\App\Services\PhonePeService::class);

        // Find orders pending payment in the last X minutes but not too recent (give 5 mins buffer)
        $orders = \App\Models\CustomerOrder::where('payment_method', 'phonepe')
            ->where('status', 'pending_payment')
            ->whereNotNull('merchant_order_id')
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->where('created_at', '<=', now()->subMinutes(5)) // Give user 5 mins to complete
            ->get();

        $this->info("Found {$orders->count()} pending orders to check.");

        $processed = 0;
        $updated = 0;

        foreach ($orders as $order) {
            $this->line("Checking Order #{$order->id} ({$order->merchant_order_id})...");

            try {
                // Check status using service
                $response = $phonePeService->checkOrderStatus($order->merchant_order_id);

                if ($response && isset($response->code) && $response->code === 'PAYMENT_SUCCESS') {
                    // Payment Success
                    $order->status = 'processing';
                    $order->payment_status = 'paid';
                    $order->phonepe_transaction_id = $response->data->transactionId ?? null;
                    $order->save();

                    $this->info("Order #{$order->id} MARKED AS PAID.");
                    $updated++;
                } elseif ($response && isset($response->code) && ($response->code === 'PAYMENT_ERROR' || $response->code === 'PAYMENT_DECLINED')) {
                    // Payment Failed
                    $order->status = 'payment_failed';
                    $order->payment_status = 'failed';
                    $order->save();

                    $this->warn("Order #{$order->id} MARKED AS FAILED.");
                    $updated++;
                } else {
                    $this->line("Order #{$order->id} status: " . ($response->code ?? 'UNKNOWN'));
                }

                $processed++;
            } catch (\Exception $e) {
                $this->error("Error processing Order #{$order->id}: " . $e->getMessage());
            }

            // Rate limit slightly
            usleep(200000); // 200ms
        }

        $this->info("Reconciliation complete. Processed {$processed}, Updated {$updated} orders.");
    }
}
