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
        Schema::create('parser_products', function (Blueprint $table) {
            $table->id();
            $table->integer('maker_id')->nullable();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('url')->default('');
            $table->string('model')->default('');
            $table->decimal('price_base', 10, 4);
            $table->decimal('price_sell', 10, 4);

            $table->boolean('fragile')->default(false);
            $table->boolean('sanctioned')->default(false);
            $table->boolean('availability')->default(true);

            $table->json('composite');
            $table->json('quantity');
            $table->json('data');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parser_products');
    }
};
