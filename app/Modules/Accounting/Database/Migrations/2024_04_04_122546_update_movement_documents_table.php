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
            $table->integer('expense_id')->nullable();
            $table->integer('status');
            $table->dropColumn('order_id');
            $table->dropColumn('completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movement_documents', function (Blueprint $table) {
            $table->dropColumn('expense_id');
            $table->dropColumn('status');
            $table->integer('order_id')->nullable();
            $table->boolean('completed')->default(false);
        });
    }
};
