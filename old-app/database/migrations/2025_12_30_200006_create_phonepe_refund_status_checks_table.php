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
        Schema::create('phonepe_refund_status_checks', function (Blueprint $table) {
            $table->id();

            // Refund identifier
            $table->string('merchant_refund_id', 64);

            // Status
            $table->enum('state', ['PENDING', 'CONFIRMED', 'COMPLETED', 'FAILED'])->nullable();

            // Full response
            $table->json('response')->nullable();

            // Timestamp
            $table->timestamp('checked_at')->useCurrent();

            // Indexes
            $table->index('merchant_refund_id');
            $table->index('checked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phonepe_refund_status_checks');
    }
};
