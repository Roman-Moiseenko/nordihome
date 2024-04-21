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
        Schema::table('pricing_products', function (Blueprint $table) {
            $table->float('price_cost', 10, 2)->default(0);
            $table->float('price_min', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricing_products', function (Blueprint $table) {
            $table->dropColumn('price_cost');
            $table->dropColumn('price_min');
        });
    }
};
