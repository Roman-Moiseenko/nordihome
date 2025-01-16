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
        Schema::table('movement_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('expense_id')->nullable()->change();
            $table->foreign('expense_id')->references('id')->on('order_expenses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('movement_documents', function (Blueprint $table) {
            $table->dropForeign(['expense_id']);
        });
        Schema::table('movement_documents', function (Blueprint $table) {
            $table->integer('expense_id')->change();
        });
    }
};
