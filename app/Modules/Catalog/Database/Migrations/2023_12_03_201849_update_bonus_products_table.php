<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bonus_products', function (Blueprint $table) {
            $table->dropForeign(['bonus_id']);
            $table->dropColumn('bonus_id');
        });

        Schema::table('bonus_products', function (Blueprint $table) {
            $table->foreignId('bonus_id')->unique()->constrained('products')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bonus_products', function (Blueprint $table) {
            $table->dropForeign(['bonus_id']);
            $table->dropColumn('bonus_id');
        });

        Schema::table('bonus_products', function (Blueprint $table) {
            $table->foreignId('bonus_id')->constrained('products')->onDelete('restrict');
        });

    }
};
