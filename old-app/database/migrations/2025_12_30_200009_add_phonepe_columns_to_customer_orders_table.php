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
        Schema::table('customer_orders', function (Blueprint $table) {
            // Add PhonePe-related columns
            $table->string('merchant_order_id', 64)->unique()->nullable()->after('id');
            $table->string('phonepe_order_id', 64)->nullable()->after('merchant_order_id');
            $table->string('phonepe_transaction_id', 64)->nullable()->after('phonepe_order_id');
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])
                ->default('pending')
                ->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dropColumn(['merchant_order_id', 'phonepe_order_id', 'phonepe_transaction_id', 'payment_status']);
        });
    }
};
