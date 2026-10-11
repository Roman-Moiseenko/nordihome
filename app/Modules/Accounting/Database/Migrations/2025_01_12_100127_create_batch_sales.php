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
        Schema::create('batch_sales', function (Blueprint $table) {
            $table->id();

            $table->foreignId('arrival_product_id')->nullable()->constrained('arrival_products')->onDelete('restrict');
            $table->foreignId('surplus_product_id')->nullable()->constrained('surplus_products')->onDelete('restrict');

            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('restrict');
            $table->decimal('quantity', 10, 3);
            $table->decimal('cost', 10, 3);

            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('restrict');
            $table->foreignId('expense_id')->nullable()->constrained('order_expenses')->onDelete('restrict');
            $table->integer('sell_cost')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_sales');
    }
};
