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
            $table->unsignedBigInteger('storage_id')->change();
            $table->foreign('storage_id')->nullable()->references('id')->on('storages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_expenses', function (Blueprint $table) {
            $table->dropForeign(['storage_id']);
        });
        Schema::table('order_expenses', function (Blueprint $table) {
            $table->integer('storage_id')->change();
        });
    }
};
