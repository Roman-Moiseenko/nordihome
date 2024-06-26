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
        Schema::drop('delivery_orders');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->integer('type');
            $table->string('address');
            $table->decimal('cost', 10, 2)->default(0.0);
            $table->timestamps();
            $table->integer('point_storage_id')->nullable();
        });
    }
};
