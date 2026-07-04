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
        // 1) Удаляем внешний ключ brand_id
        Schema::table('parser_categories', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
        });

        // 2) Удаляем старый уникальный индекс с url (если он есть),
        //    переименовываем url -> ikea_id и добавляем unique заново
        Schema::table('parser_categories', function (Blueprint $table) {
            $table->dropUnique(['url']);
        });

        Schema::table('parser_categories', function (Blueprint $table) {
            $table->renameColumn('url', 'ikea_id');
        });

        Schema::table('parser_categories', function (Blueprint $table) {
            $table->unique('ikea_id');
        });

        // 3) Удаляем саму колонку brand_id
        Schema::table('parser_categories', function (Blueprint $table) {
            $table->dropColumn('brand_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Возвращаем brand_id
        Schema::table('parser_categories', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->constrained('brands')->cascadeOnDelete();
        });

        // Возвращаем url
        Schema::table('parser_categories', function (Blueprint $table) {
            $table->dropUnique(['ikea_id']);
        });

        Schema::table('parser_categories', function (Blueprint $table) {
            $table->renameColumn('ikea_id', 'url');
        });

        Schema::table('parser_categories', function (Blueprint $table) {
            $table->unique('url');
        });
    }
};
