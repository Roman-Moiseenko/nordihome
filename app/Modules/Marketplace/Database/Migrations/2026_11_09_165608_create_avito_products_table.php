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
        Schema::create('avito_products', function (Blueprint $table) {
            $table->id();
            // Номер объявления на Авито
            $table->string('avito_id')->nullable()->index();
            // Связь с товаром каталога
            $table->unsignedBigInteger('product_id')->nullable();
            // Категория Авито
            $table->unsignedBigInteger('avito_category_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('price')->default(0);
            $table->unsignedInteger('price_package')->default(0);
            $table->string('tn_ved')->nullable();
            $table->string('okpd2')->nullable();
            // Автодоставка
            $table->boolean('delivery')->default(false);
            // Активность объявления
            $table->boolean('active')->default(false);
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->nullOnDelete();

            $table->foreign('avito_category_id')
                ->references('id')
                ->on('avito_categories')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avito_products');
    }
};
