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
        Schema::table('product_parser', function (Blueprint $table) {
            $table->boolean('fragile')->default(false);
            $table->boolean('sanctioned')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_parser', function (Blueprint $table) {
            $table->dropColumn('fragile');
            $table->dropColumn('sanctioned');
        });
    }
};
