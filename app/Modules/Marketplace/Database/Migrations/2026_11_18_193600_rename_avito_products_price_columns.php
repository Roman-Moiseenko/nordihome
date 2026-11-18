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
        Schema::table('avito_products', function (Blueprint $table) {
            // Базовая цена товара (без учёта упаковки и комиссии Авито).
            $table->renameColumn('price', 'base_price');
            // Цена продажи (ручная). Если null — считается калькулятором.
            $table->unsignedInteger('sell_price')->nullable()->after('base_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('avito_products', function (Blueprint $table) {
            $table->dropColumn('sell_price');
            $table->renameColumn('base_price', 'price');
        });
    }
};
