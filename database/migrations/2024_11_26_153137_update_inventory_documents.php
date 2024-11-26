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
        Schema::table('inventory_documents', function (Blueprint $table) {
            $table->dropForeign(['arrival_id']);
        });

        Schema::table('inventory_documents', function (Blueprint $table) {
            $table->dropColumn('arrival_id');
        });

        Schema::table('inventory_documents', function (Blueprint $table) {
            $table->foreignId('surplus_id')->nullable()->constrained('surplus_documents')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_documents', function (Blueprint $table) {
            $table->foreignId('arrival_id')->nullable()->constrained('arrival_documents')->onDelete('set null');
        });

        Schema::table('inventory_documents', function (Blueprint $table) {
            $table->dropForeign(['surplus_id']);
        });

        Schema::table('inventory_documents', function (Blueprint $table) {
            $table->dropColumn('surplus_id');
        });

    }
};
