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
        Schema::create('storages_movements', function (Blueprint $table) {
            $table->foreignId('storage_item_id')->constrained('storage_items')->onDelete('cascade');
            $table->foreignId('movement_item_id')->constrained('movement_products')->onDelete('cascade');
            $table->integer('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('storages_movements');
    }
};
