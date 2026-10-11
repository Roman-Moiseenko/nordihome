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
        Schema::table('tags_products', function (Blueprint $table) {
            $table->dropForeign(['tag_id']);
            $table->dropForeign(['product_id']);
        });
        Schema::table('tags_products', function (Blueprint $table) {
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tags_products', function (Blueprint $table) {
            $table->dropForeign(['tag_id']);
            $table->dropForeign(['product_id']);
        });
        Schema::table('tags_products', function (Blueprint $table) {
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('restrict');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
        });

    }
};
