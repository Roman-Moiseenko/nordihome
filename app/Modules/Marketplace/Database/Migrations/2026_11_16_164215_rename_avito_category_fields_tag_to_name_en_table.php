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
        Schema::table('avito_category_fields', function (Blueprint $table) {
            // Снимаем внешний ключ, т.к. MySQL использует уникальный индекс
            // (avito_category_id, tag) для его поддержки.
            $table->dropForeign(['avito_category_id']);
            $table->dropUnique(['avito_category_id', 'tag']);

            // tag -> name_en: теперь хранится английское название поля из шаблона.
            $table->renameColumn('tag', 'name_en');
        });

        Schema::table('avito_category_fields', function (Blueprint $table) {
            // Приводим длину к длине name_en в реестре параметров.
            $table->string('name_en', 255)->change();

            $table->unique(['avito_category_id', 'name_en']);

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
        Schema::table('avito_category_fields', function (Blueprint $table) {
            $table->dropForeign(['avito_category_id']);
            $table->dropUnique(['avito_category_id', 'name_en']);

            $table->renameColumn('name_en', 'tag');
        });

        Schema::table('avito_category_fields', function (Blueprint $table) {
            $table->string('tag', 100)->change();

            $table->unique(['avito_category_id', 'tag']);

            $table->foreign('avito_category_id')
                ->references('id')
                ->on('avito_categories')
                ->cascadeOnDelete();
        });
    }
};
