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
        Schema::table('parser_categories', function (Blueprint $table) {
            $table->string('slug')->nullable();
        });
        Schema::table('parser_products', function (Blueprint $table) {
            $table->string('slug')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parser_categories', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
        Schema::table('parser_products', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
