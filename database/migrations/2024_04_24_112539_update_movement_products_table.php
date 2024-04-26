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
        Schema::table('movement_products', function (Blueprint $table) {
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->onDelete('set null');
            $table->dropColumn('cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movement_products', function (Blueprint $table) {
            $table->float('cost');
            $table->dropForeign(['order_item_id']);
            $table->dropColumn('order_item_id');
        });
    }
};
