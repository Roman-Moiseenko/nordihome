<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('order_expense_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')->constrained('order_expenses')->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
            $table->integer('quantity');
        });
    }

    public function down(): void
    {
        Schema::drop('order_expense_items');
    }
};
