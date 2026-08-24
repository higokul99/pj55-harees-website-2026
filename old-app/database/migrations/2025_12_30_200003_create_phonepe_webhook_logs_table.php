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
        Schema::create('phonepe_webhook_logs', function (Blueprint $table) {
            $table->id();

            // Event details
            $table->string('event_type', 64)->nullable();
            $table->string('merchant_order_id', 64)->nullable();
            $table->string('phonepe_order_id', 64)->nullable();
            $table->string('merchant_refund_id', 64)->nullable();

            // Security
            $table->string('authorization_header')->nullable();
            $table->boolean('verified')->default(false);

            // Raw payload (CRITICAL - never delete)
            $table->json('payload');

            // Processing tracking
            $table->timestamp('received_at')->useCurrent();
            $table->enum('processing_status', ['RECEIVED', 'PROCESSED', 'FAILED'])->default('RECEIVED');
            $table->text('failure_reason')->nullable();

            // Indexes
            $table->index('merchant_order_id');
            $table->index('event_type');
            $table->index('processing_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phonepe_webhook_logs');
    }
};
