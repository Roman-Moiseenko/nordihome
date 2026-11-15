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
        Schema::create('avito_common_images', function (Blueprint $table) {
            $table->id();
            // Название изображения.
            $table->string('name')->nullable();
            // Активность отдельного изображения.
            $table->boolean('active')->default(true);
            // Глобальный порядок сортировки среди общих изображений.
            $table->integer('sort')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avito_common_images');
    }
};
