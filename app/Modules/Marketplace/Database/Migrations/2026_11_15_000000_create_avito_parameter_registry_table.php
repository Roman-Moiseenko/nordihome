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
        Schema::create('avito_parameter_registry', function (Blueprint $table) {
            $table->id();
            // Английское название поля из шаблона (ключ сопоставления).
            $table->string('name_en')->unique();
            // Русское название поля (только для вывода в админке).
            $table->string('name_ru')->nullable();
            // Вид параметра: product | common | skip | category (null — не определён).
            $table->string('kind', 20)->nullable();
            // Имя поля сущности AvitoProductEntity (только для kind = product).
            $table->string('field', 100)->nullable();
            // Тип: string | integer | float | array (nullable — уточняется вручную).
            $table->string('type', 50)->nullable();
            // Обязательность.
            $table->boolean('is_required')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avito_parameter_registry');
    }
};
