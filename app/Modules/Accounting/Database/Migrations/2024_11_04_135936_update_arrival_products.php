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
             $table->dropColumn('cost_ru');
             $table->dropColumn('price_sell');
             $table->integer('remains')->default(0);
        });
        Schema::table('arrival_products', function (Blueprint $table) {
            $table->index(['product_id', 'remains']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arrival_products', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });
        Schema::table('arrival_products', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'remains']);
        });
        Schema::table('arrival_products', function (Blueprint $table) {
            $table->float('cost_ru');
            $table->integer('price_sell');
            $table->dropColumn('remains');
        });
        Schema::table('arrival_products', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
};
