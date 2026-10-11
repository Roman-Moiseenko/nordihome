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
        Schema::table('supply_products', function (Blueprint $table) {
            $table->decimal('pre_cost', 12, 4)->nullable();
            $table->decimal('cost_currency', 12, 4)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supply_products', function (Blueprint $table) {
            $table->dropColumn('pre_cost');
            $table->decimal('cost_currency', 8, 2)->change();
        });
    }
};
