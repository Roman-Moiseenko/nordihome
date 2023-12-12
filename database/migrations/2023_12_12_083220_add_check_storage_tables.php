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
        Schema::table('cart_storage', function (Blueprint $table) {
            $table->boolean('check')->default(true);
        });
        Schema::table('cart_cookie', function (Blueprint $table) {
            $table->boolean('check')->default(true);
        });
        //cart_cookie
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_storage', function (Blueprint $table) {
            $table->dropColumn('check');
        });
        Schema::table('cart_cookie', function (Blueprint $table) {
            $table->dropColumn('check');
        });
    }
};
