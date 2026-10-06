<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modifications', function (Blueprint $table) {
            $table->dropForeign(['base_product_id']);
        });

        Schema::table('modifications', function (Blueprint $table) {
            $table->dropColumn(['base_product_id', 'attributes_json']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('modifications', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('modifications', function (Blueprint $table) {
            $table->unsignedBigInteger('base_product_id')->nullable();
            $table->json('attributes_json')->nullable();
        });

        Schema::table('modifications', function (Blueprint $table) {
            $table->foreign('base_product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
};
