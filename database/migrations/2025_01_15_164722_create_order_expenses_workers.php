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
        Schema::create('order_expenses_workers', function (Blueprint $table) {
            $table->foreignId('expense_id')->constrained('order_expenses')->onDelete('cascade');
            $table->foreignId('worker_id')->constrained('workers')->onDelete('restrict');
            $table->integer('work');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_expenses_workers');
    }
};
