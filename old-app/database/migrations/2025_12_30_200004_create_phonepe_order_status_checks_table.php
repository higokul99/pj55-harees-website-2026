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
        Schema::create('phonepe_order_status_checks', function (Blueprint $table) {
            $table->id();

            // Order identifiers
            $table->string('merchant_order_id', 64);
            $table->string('phonepe_order_id', 64)->nullable();

            // Status details
            $table->enum('state', ['PENDING', 'COMPLETED', 'FAILED'])->nullable();
            $table->bigInteger('amount')->nullable();

            // Full response
            $table->json('response')->nullable();

            // Timestamp
            $table->timestamp('checked_at')->useCurrent();

            // Indexes
            $table->index('merchant_order_id');
            $table->index('checked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phonepe_order_status_checks');
    }
};
