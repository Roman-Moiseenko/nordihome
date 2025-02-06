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
        Schema::drop('order_refund_additions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('order_refund_additions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('refund_id')->constrained('order_refunds')->onDelete('cascade');
            $table->foreignId('order_addition_id')->constrained('order_additions')->onDelete('cascade');
            $table->float('amount');
        });
    }
};
