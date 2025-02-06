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
        Schema::table('order_expense_refunds', function (Blueprint $table) {
            $table->dropColumn('retention');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_expense_refunds', function (Blueprint $table) {
            $table->decimal('retention', 10, 3)->default(0);

        });
    }
};
