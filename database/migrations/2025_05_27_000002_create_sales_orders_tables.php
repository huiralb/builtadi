<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('address');
                $table->string('phone');
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->decimal('production_price', 12, 2);
                $table->decimal('selling_price', 12, 2);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sales_orders')) {
            Schema::create('sales_orders', function (Blueprint $table) {
                $table->id();
                $table->string('reference_no')->unique();
                $table->foreignId('sales_id')->constrained('sales');
                $table->foreignId('customer_id')->constrained();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sales_order_items')) {
            Schema::create('sales_order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('sales_orders')->onDelete('cascade');
                $table->foreignId('product_id')->constrained();
                $table->integer('quantity');
                $table->decimal('production_price', 20, 2);
                $table->decimal('selling_price', 20, 2);
            });
        }


    }

    public function down(): void
    {
        Schema::dropIfExists('sales_order_items');
        Schema::dropIfExists('sales_orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('customers');
    }
};
