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

        Schema::table('modifications', function (Blueprint $table) {
            $table->dropForeign(['base_product_id']);
        });

        Schema::table('modifications', function (Blueprint $table) {
            $table->foreign('base_product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modifications', function (Blueprint $table) {
            $table->dropForeign(['base_product_id']);
        });

        Schema::table('modifications', function (Blueprint $table) {
            $table->foreign('base_product_id')->references('id')->on('products')->onDelete('restrict');
        });
    }
};
