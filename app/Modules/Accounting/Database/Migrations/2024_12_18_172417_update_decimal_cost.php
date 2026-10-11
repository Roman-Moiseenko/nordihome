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
        Schema::table('arrival_products', function (Blueprint $table) {
            $table->decimal('cost_currency', 12, 4)->change();
        });

        Schema::table('distributors_products', function (Blueprint $table) {
            $table->decimal('cost', 12, 4)->change();
            $table->decimal('pre_cost', 12, 4)->nullable()->change();
        });

        Schema::table('refund_products', function (Blueprint $table) {
            $table->decimal('cost_currency', 12, 4)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supply_products', function (Blueprint $table) {
            $table->decimal('cost_currency', 8, 2)->change();
        });

        Schema::table('distributors_products', function (Blueprint $table) {
            $table->decimal('cost', 8, 2)->change();
            $table->decimal('pre_cost', 8, 2)->nullable()->change();
        });

        Schema::table('refund_products', function (Blueprint $table) {
            $table->decimal('cost_currency', 8, 2)->change();
        });
    }
};
