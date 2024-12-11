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
        Schema::table('arrival_products', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });

        Schema::table('arrival_expense_items', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });
        Schema::table('departure_products', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });
        Schema::table('inventory_products', function (Blueprint $table) {
            $table->decimal('formal', 10, 3)->change();
            $table->decimal('quantity', 10, 3)->change();
        });
        Schema::table('movement_products', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });
        Schema::table('refund_products', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });
        Schema::table('storage_items', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });
        Schema::table('storage_arrival_items', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });
        Schema::table('storage_departure_items', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });
        Schema::table('supply_products', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });
        Schema::table('supply_stack', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });
        Schema::table('surplus_products', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });

        Schema::table('order_expense_items', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });
        Schema::table('order_refund_items', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });
        Schema::table('order_reserve', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });

        Schema::table('cart_cookie', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });
        Schema::table('cart_storage', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });
        Schema::table('parser_storage', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });
        Schema::table('composites', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });
        Schema::table('bonus_quantity', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arrival_products', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('arrival_expense_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
        Schema::table('departure_products', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
        Schema::table('inventory_products', function (Blueprint $table) {
            $table->integer('quantity')->change();
            $table->integer('formal')->change();
        });
        Schema::table('movement_products', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
        Schema::table('refund_products', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
        Schema::table('storage_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
        Schema::table('storage_arrival_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
        Schema::table('storage_departure_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
        Schema::table('supply_products', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
        Schema::table('supply_stack', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
        Schema::table('surplus_products', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('order_expense_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
        Schema::table('order_refund_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
        Schema::table('order_reserve', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('cart_cookie', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
        Schema::table('cart_storage', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
        Schema::table('parser_storage', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
        Schema::table('composites', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
        Schema::table('bonus_quantity', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
    }
};
