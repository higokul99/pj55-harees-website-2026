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
        Schema::create('phonepe_orders', function (Blueprint $table) {
            $table->id();

            // Merchant and PhonePe identifiers
            $table->string('merchant_order_id', 64)->unique();
            $table->string('phonepe_order_id', 64)->nullable();

            // Amount details
            $table->bigInteger('amount'); // Amount in paisa
            $table->string('currency', 10)->default('INR');

            // State tracking
            $table->enum('state', ['PENDING', 'COMPLETED', 'FAILED'])->default('PENDING');
            $table->enum('last_known_state', ['PENDING', 'COMPLETED', 'FAILED'])->default('PENDING');

            // Expiry and redirect
            $table->bigInteger('expire_at')->nullable(); // Epoch milliseconds
            $table->text('redirect_url')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('state');
            $table->index('phonepe_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phonepe_orders');
    }
};
