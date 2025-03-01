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
        //Все таблицы с документами
        Schema::table('inventory_documents', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('refund_documents', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('arrival_documents', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('movement_documents', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('supply_documents', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('arrival_expense_documents', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('pricing_documents', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('departure_documents', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('surplus_documents', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_documents', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('refund_documents', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('arrival_documents', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('movement_documents', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('supply_documents', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('arrival_expense_documents', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('pricing_documents', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('departure_documents', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('surplus_documents', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
