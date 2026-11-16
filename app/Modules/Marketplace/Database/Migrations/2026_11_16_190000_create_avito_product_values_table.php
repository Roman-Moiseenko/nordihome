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
        Schema::create('avito_product_values', function (Blueprint $table) {
            $table->id();
            // Объявление Авито
            $table->unsignedBigInteger('avito_product_id');
            // Параметр категории Авито
            $table->unsignedBigInteger('avito_category_field_id');
            // Значение параметра (для array хранится JSON)
            $table->text('value')->nullable();
            $table->timestamps();

            $table->foreign('avito_product_id')
                ->references('id')
                ->on('avito_products')
                ->cascadeOnDelete();

            $table->foreign('avito_category_field_id')
                ->references('id')
                ->on('avito_category_fields')
                ->cascadeOnDelete();

            $table->unique(['avito_product_id', 'avito_category_field_id'], 'avito_product_field_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avito_product_values');
    }
};
