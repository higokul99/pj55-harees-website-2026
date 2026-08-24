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
        Schema::create('phonepe_payment_attempts', function (Blueprint $table) {
            $table->id();

            // Order references
            $table->string('merchant_order_id', 64);
            $table->string('transaction_id', 64)->nullable(); // PhonePe transaction ID

            // Payment details
            $table->string('payment_mode', 32)->nullable(); // UPI_INTENT, CARD, NET_BANKING, etc.
            $table->enum('state', ['PENDING', 'COMPLETED', 'FAILED'])->nullable();
            $table->bigInteger('amount')->nullable(); // Amount in paisa
            $table->bigInteger('timestamp')->nullable(); // Epoch milliseconds

            // Error tracking
            $table->string('error_code', 64)->nullable();
            $table->string('detailed_error_code', 128)->nullable();

            // Raw response storage
            $table->json('raw_response')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('transaction_id');
            $table->index('merchant_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phonepe_payment_attempts');
    }
};
