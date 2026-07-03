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
        Schema::table('categories', function (Blueprint $table) {
            $table->integer('wp_id')->nullable();
        });
        Schema::table('rooms', function (Blueprint $table) {
            $table->integer('wp_id')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('wp_id');
        });
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('wp_id');
        });
    }
};
