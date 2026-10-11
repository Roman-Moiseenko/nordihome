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
        Schema::create('order_expense_refunds', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable();
            $table->string('comment')->default('');

            //$table->decimal('amount', 10, 3)->nullable();
            $table->decimal('retention', 10, 3)->default(0);
            $table->integer('reason')->nullable();
            $table->boolean('completed')->default(false);

            $table->foreignId('staff_id')->constrained('admins')->onDelete('restrict');
            $table->foreignId('expense_id')->constrained('order_expenses')->onDelete('cascade');
            $table->foreignId('order_payment_id')->nullable()->constrained('order_payments')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_expense_refunds');
    }
};
