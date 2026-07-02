<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Создать таблицу rooms_products и удалить products_rooms.
     */
    public function up(): void
    {
        Schema::create('rooms_products', function (Blueprint $table) {
            $table->foreignId('room_id')
                ->constrained('rooms')
                ->onDelete('cascade');
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');
        });

        Schema::dropIfExists('products_rooms');
    }

    /**
     * Reverse the migrations.
     * Восстановить products_rooms и удалить rooms_products.
     */
    public function down(): void
    {
        Schema::create('products_rooms', function (Blueprint $table) {
            $table->foreignId('room_id')
                ->constrained('rooms')
                ->onDelete('cascade');
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');
        });

        Schema::dropIfExists('rooms_products');
    }
};
