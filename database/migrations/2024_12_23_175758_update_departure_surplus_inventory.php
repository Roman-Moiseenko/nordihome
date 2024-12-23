<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departure_documents', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->constrained('organizations')->onDelete('restrict');
        });
        Schema::table('surplus_documents', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->constrained('organizations')->onDelete('restrict');
        });
        Schema::table('inventory_documents', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->constrained('organizations')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('departure_documents', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });
        Schema::table('departure_documents', function (Blueprint $table) {
            $table->dropColumn('customer_id');
        });
        Schema::table('surplus_documents', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });
        Schema::table('surplus_documents', function (Blueprint $table) {
            $table->dropColumn('customer_id');
        });
        Schema::table('inventory_documents', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });
        Schema::table('inventory_documents', function (Blueprint $table) {
            $table->dropColumn('customer_id');
        });
    }
};
