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
        Schema::table('order_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->change();
            $table->foreignId('shopper_id')->nullable()->constrained('organizations')->onDelete('restrict');
            $table->foreignId('trader_id')->nullable()->constrained('organizations')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->change();
            $table->dropForeign(['shopper_id']);
            $table->dropForeign(['trader_id']);
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->dropColumn('shopper_id');
            $table->dropColumn('trader_id');
        });
    }
};
