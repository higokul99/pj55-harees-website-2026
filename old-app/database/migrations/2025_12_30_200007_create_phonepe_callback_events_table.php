<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('phonepe_callback_events', function (Blueprint $table) {
            $table->id();

            // Event type
            $table->enum('event', [
                'CHECKOUT_ORDER_COMPLETED',
                'CHECKOUT_ORDER_FAILED',
                'PG_REFUND_COMPLETED',
                'PG_REFUND_FAILED',
                'PG_REFUND_ACCEPTED'
            ]);

            // References
            $table->string('merchant_order_id', 64)->nullable();
            $table->string('merchant_refund_id', 64)->nullable();

            // Event details
            $table->string('state', 32)->nullable();
            $table->bigInteger('amount')->nullable();
            $table->bigInteger('occurred_at')->nullable(); // Epoch milliseconds

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('event');
            $table->index('merchant_order_id');
            $table->index('occurred_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phonepe_callback_events');
    }
};
