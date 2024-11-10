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
        Schema::table('supply_documents', function (Blueprint $table) {
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supply_documents', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
        });
        Schema::table('supply_documents', function (Blueprint $table) {
            $table->dropColumn('currency_id');
        });
    }
};
