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
        Schema::table('order_additions', function (Blueprint $table) {
            $table->foreignId('addition_id')->constrained('guide_addition')->onDelete('restrict');
            $table->integer('quantity')->default(1);
            $table->dropColumn('purpose');
            $table->integer('amount')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_additions', function (Blueprint $table) {
            $table->dropForeign(['addition_id']);
        });
        Schema::table('order_additions', function (Blueprint $table) {
            $table->dropColumn('addition_id');
            $table->dropColumn('quantity');
            $table->integer('purpose')->nullable();
            $table->float('amount')->change();
        });
    }
};
