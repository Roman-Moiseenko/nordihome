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
            $table->foreignId('customer_id')->nullable()->constrained('organizations')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supply_documents', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });
        Schema::table('supply_documents', function (Blueprint $table) {
            $table->dropColumn('customer_id');
        });
    }
};
