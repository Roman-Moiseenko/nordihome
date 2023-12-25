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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->integer('type');
            $table->boolean('preorder');
            $table->boolean('paid')->default(false);
            $table->boolean('finished')->default(false);
            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('coupon', 10, 2)->default(0);
            $table->integer('coupon_id')->nullable();
            $table->decimal('delivery_cost', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('orders');
    }
};
