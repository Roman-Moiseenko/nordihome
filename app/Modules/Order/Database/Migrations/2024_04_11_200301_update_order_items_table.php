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
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('supply_stack_id');
            $table->dropColumn('supplier_document_id');

        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('supply_stack_id')->nullable()->constrained('supply_stack')->onDelete('set null');
        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['supply_stack_id']);
            $table->dropColumn('supply_stack_id');
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('supply_stack_id')->nullable();
            $table->integer('supplier_document_id')->nullable();
        });
    }
};
