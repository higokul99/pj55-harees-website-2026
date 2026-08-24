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
        Schema::create('phonepe_refunds', function (Blueprint $table) {
            $table->id();

            // Refund identifiers
            $table->string('merchant_refund_id', 64)->unique();
            $table->string('merchant_order_id', 64);
            $table->string('phonepe_refund_id', 64)->nullable();

            // Refund details
            $table->bigInteger('amount'); // Amount in paisa
            $table->enum('state', ['PENDING', 'CONFIRMED', 'COMPLETED', 'FAILED'])->default('PENDING');

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('merchant_order_id');
            $table->index('state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phonepe_refunds');
    }
};
