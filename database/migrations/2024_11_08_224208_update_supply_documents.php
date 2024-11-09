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
            $table->float('exchange_fix', 8,4,true)->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supply_documents', function (Blueprint $table) {
            $table->float('exchange_fix')->change();
        });
    }
};
