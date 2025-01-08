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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('trader_id')->constrained('organizations')->onDelete('restrict');
            $table->foreignId('shopper_id')->nullable()->constrained('organizations')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['trader_id']);
            $table->dropForeign(['shopper_id']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('trader_id');
            $table->dropColumn('shopper_id');
        });
    }
};
