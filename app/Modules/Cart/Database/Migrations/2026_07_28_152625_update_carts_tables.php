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
        Schema::table('cart_cookie', function (Blueprint $table) {
            $table->boolean('is_parser')->default(false);
        });

        Schema::table('cart_storage', function (Blueprint $table) {
            $table->boolean('is_parser')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_cookie', function (Blueprint $table) {
            $table->dropColumn('is_parser');
        });

        Schema::table('cart_storage', function (Blueprint $table) {
            $table->dropColumn('is_parser');
        });
    }
};
