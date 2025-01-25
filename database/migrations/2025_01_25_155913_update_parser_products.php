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
        Schema::table('parser_products', function (Blueprint $table) {
            $table->decimal('price_base', 10, 4)->default(0.0)->change();
            $table->decimal('price_sell', 10, 4)->default(0.0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parser_products', function (Blueprint $table) {
            $table->decimal('price_base', 10, 4)->change();
            $table->decimal('price_sell', 10, 4)->change();
        });
    }
};
