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
        Schema::table('arrival_documents', function (Blueprint $table) {
            $table->foreignId('supply_id')->nullable()->constrained('supply_documents')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arrival_documents', function (Blueprint $table) {
            $table->dropForeign(['supply_id']);
            $table->dropColumn('supply_id');
        });
    }
};
