<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('avito_products', function (Blueprint $table) {
            // Ссылки на фото из выгрузки Авито (как есть, одной строкой).
            $table->string('avito_images')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('avito_products', function (Blueprint $table) {
            $table->dropColumn('avito_images');
        });
    }
};
