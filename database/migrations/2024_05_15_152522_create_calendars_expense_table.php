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
        Schema::create('calendars_expenses', function (Blueprint $table) {
            $table->foreignId('period_id')->constrained('calendar_periods')->onDelete('cascade');
            $table->foreignId('expense_id')->constrained('order_expenses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('calendars_expenses');
    }
};
