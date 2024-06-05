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
        Schema::table('', function (Blueprint $table) {
            //
        });

        Schema::table('related_products', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });
        Schema::table('related_products', function (Blueprint $table) {
            $table->dropForeign(['related_id']);
        });

        Schema::table('related_products', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('related_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('related_products', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });
        Schema::table('related_products', function (Blueprint $table) {
            $table->dropForeign(['related_id']);
        });

        Schema::table('related_products', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            $table->foreign('related_id')->references('id')->on('products')->onDelete('restrict');
        });
    }
};
