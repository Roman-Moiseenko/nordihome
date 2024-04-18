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
        Schema::create('pricing_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pricing_id')->constrained('pricing_documents')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->float('price_cost', 10, 2)->default(0);
            $table->float('price_retail', 10, 2)->default(0);
            $table->float('price_bunk', 10, 2)->default(0);
            $table->float('price_special', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('pricing_products');
    }
};
