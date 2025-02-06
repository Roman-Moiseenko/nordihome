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
        Schema::create('order_expense_refund_items', function (Blueprint $table) {
            $table->id();
            $table->decimal('quantity', 10, 3);
            $table->foreignId('refund_id')->constrained('order_expense_refunds')->onDelete('cascade');
            $table->foreignId('expense_item_id')->constrained('order_expense_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_expense_refund_items');
    }
};
