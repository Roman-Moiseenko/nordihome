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
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->string('incoming_number')->default('');
            $table->timestamp('incoming_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->dropColumn('incoming_number');
            $table->dropColumn('incoming_at');
        });
    }
};
