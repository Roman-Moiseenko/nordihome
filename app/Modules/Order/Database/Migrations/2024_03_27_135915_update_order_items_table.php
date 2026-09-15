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
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('first_quantity');
            $table->boolean('delivery')->default(false);
            $table->boolean('assemblage')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('first_quantity')->nullable();
            $table->dropColumn('delivery');
            $table->dropColumn('assemblage');
        });
    }
};
