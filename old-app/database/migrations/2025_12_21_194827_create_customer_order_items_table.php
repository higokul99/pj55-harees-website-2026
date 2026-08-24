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
        Schema::create('customer_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_code');
            $table->string('table_name');
            $table->integer('quantity');
            $table->decimal('price', 12, 2); // Unit price (calculated_price)
            $table->decimal('metal_cost', 12, 2)->default(0);
            $table->decimal('making_charges', 12, 2)->default(0);
            $table->decimal('gst', 12, 2)->default(0);
            $table->string('metal_type')->nullable();
            $table->timestamps();
            
            $table->foreign('order_id')->references('id')->on('customer_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_order_items');
    }
};
