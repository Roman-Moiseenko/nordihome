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
        Schema::table('order_expenses', function (Blueprint $table) {
            $table->dropForeign(['worker_id']);
        });
        Schema::table('order_expenses', function (Blueprint $table) {
            $table->dropColumn('worker_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_expenses', function (Blueprint $table) {
            $table->foreignId('worker_id')->nullable()->constrained('workers')->onDelete('set null');
        });
    }
};
