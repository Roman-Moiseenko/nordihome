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
        Schema::create('equivalents_products', function (Blueprint $table) {
            $table->foreignId('equivalent_id')->constrained('equivalents')->onDelete('restrict');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equivalents_products');
    }
};
