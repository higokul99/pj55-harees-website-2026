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
        Schema::create('phonepe_security_audit', function (Blueprint $table) {
            $table->id();

            // Source tracking
            $table->enum('source', ['WEBHOOK', 'API']);
            $table->string('reference_id', 64)->nullable();

            // Authorization verification
            $table->string('authorization_received')->nullable();
            $table->string('authorization_expected')->nullable();
            $table->boolean('verified');

            // Additional info
            $table->text('remarks')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('source');
            $table->index('verified');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phonepe_security_audit');
    }
};
