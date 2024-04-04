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
        Schema::create('order_expense_additions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')->constrained('order_expenses')->onDelete('cascade');
            $table->foreignId('order_addition_id')->constrained('order_additions')->onDelete('cascade');
            $table->float('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('order_expense_additions');
    }
};
