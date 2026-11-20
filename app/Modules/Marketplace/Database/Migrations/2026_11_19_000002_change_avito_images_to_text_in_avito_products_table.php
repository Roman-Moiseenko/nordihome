<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('avito_products', function (Blueprint $table) {
            // Ссылки на фото могут содержать несколько длинных URL — нужен text.
            $table->text('avito_images')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('avito_products', function (Blueprint $table) {
            $table->string('avito_images')->nullable()->change();
        });
    }
};
