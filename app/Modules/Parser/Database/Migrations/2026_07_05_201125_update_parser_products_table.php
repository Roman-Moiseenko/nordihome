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
            $table->dropColumn('maker_id');
            $table->dropColumn('model');
            $table->dropColumn('data');
            $table->string('code');
            $table->string('name');
            $table->string('short')->default('');
            $table->string('description')->default('');
            $table->json('packages')->nullable();
            $table->json('colors')->nullable();
            $table->integer('packs')->default(1);
        });

        Schema::table('parser_products', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parser_products', function (Blueprint $table) {
            $table->string('maker_id')->nullable();
            $table->string('model')->nullable();
            $table->json('data')->nullable();
            $table->dropColumn('code');
            $table->dropColumn('name');
            $table->dropColumn('short');
            $table->dropColumn('description');
            $table->dropColumn('packages');
            $table->dropColumn('colors');
            $table->dropColumn('packs');
        });

        Schema::table('parser_products', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable(false)->change();
        });
    }
};
