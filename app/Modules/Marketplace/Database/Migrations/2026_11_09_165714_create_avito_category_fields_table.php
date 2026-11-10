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
        Schema::create('avito_category_fields', function (Blueprint $table) {
            $table->id();
            // Внешний ключ на внутренний id категории
            $table->unsignedBigInteger('avito_category_id');
            // Тег поля в XML фида (например, 'GoodsType', 'Brand')
            $table->string('tag', 100);
            // Человекочитаемое название поля для админки
            $table->string('name', 255);
            // Тип поля: input, select, checkbox
            $table->string('type', 50);
            // Обязательно ли поле для заполнения
            $table->boolean('is_required')->default(false);
            // Массив допустимых значений (для select)
            $table->json('allowed_values')->nullable();
            // Зависимости от других полей (например, 'Brand' зависит от 'Model')
            $table->json('depends_on')->nullable();
            $table->timestamps();

            // Уникальность в рамках категории
            $table->unique(['avito_category_id', 'tag']);

            $table->foreign('avito_category_id')
                ->references('id')
                ->on('avito_categories')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avito_category_fields');
    }
};
