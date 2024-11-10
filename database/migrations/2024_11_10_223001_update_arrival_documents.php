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
            $table->string('incoming_number')->default('');
            $table->timestamp('incoming_at')->nullable();
            $table->float('exchange_fix', 8,4,true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arrival_documents', function (Blueprint $table) {
            $table->dropColumn('incoming_number');
            $table->dropColumn('incoming_at');
            $table->float('exchange_fix')->change();
        });
    }
};
