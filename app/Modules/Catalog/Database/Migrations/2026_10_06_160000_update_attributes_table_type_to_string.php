<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Меняем тип колонки с integer на string
        Schema::table('attributes', function (Blueprint $table) {
            $table->string('type')->change();
        });

        // 2. Заменяем числовые значения на строковые
        DB::statement("UPDATE attributes SET type = 'string' WHERE type = '101'");
        DB::statement("UPDATE attributes SET type = 'bool' WHERE type = '102'");
        DB::statement("UPDATE attributes SET type = 'integer' WHERE type = '103'");
        DB::statement("UPDATE attributes SET type = 'variant' WHERE type = '104'");
        DB::statement("UPDATE attributes SET type = 'float' WHERE type = '105'");
        DB::statement("UPDATE attributes SET type = 'date' WHERE type = '106'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Возвращаем строковые значения обратно в числовые
        DB::statement("UPDATE attributes SET type = '101' WHERE type = 'string'");
        DB::statement("UPDATE attributes SET type = '102' WHERE type = 'bool'");
        DB::statement("UPDATE attributes SET type = '103' WHERE type = 'integer'");
        DB::statement("UPDATE attributes SET type = '104' WHERE type = 'variant'");
        DB::statement("UPDATE attributes SET type = '105' WHERE type = 'float'");
        DB::statement("UPDATE attributes SET type = '106' WHERE type = 'date'");

        // 2. Меняем тип колонки обратно на integer
        Schema::table('attributes', function (Blueprint $table) {
            $table->integer('type')->change();
        });
    }
};
