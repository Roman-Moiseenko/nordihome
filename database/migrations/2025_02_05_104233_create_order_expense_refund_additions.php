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
        Schema::create('order_expense_refund_additions', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 3);
            $table->foreignId('refund_id')->constrained('order_expense_refunds')->onDelete('cascade');
            $table->foreignId('expense_addition_id')->constrained('order_expense_additions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_expense_refund_additions');
    }
};
