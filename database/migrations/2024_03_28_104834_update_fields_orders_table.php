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
            $table->dropColumn('amount');
            $table->dropColumn('discount');
            $table->dropColumn('total');
            $table->dropColumn('delivery_cost');

            $table->integer('discount_id')->nullable();
            $table->float('manual')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('discount_id');
            $table->dropColumn('manual');

            $table->float('amount')->default(0);
            $table->float('discount')->default(0);
            $table->float('total')->default(0);
            $table->float('delivery_cost')->default(0);
        });
    }
};
