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
            $table->boolean('completed')->default(false);
            $table->float('exchange_fix');
            $table->string('incoming_number')->default('');
            $table->timestamp('incoming_at')->nullable();

            $table->string('number')->default('')->change();
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supply_documents', function (Blueprint $table) {
            $table->dropColumn('completed');
            $table->dropColumn('exchange_fix');
            $table->dropColumn('incoming_number');
            $table->dropColumn('incoming_at');

            $table->integer('number')->nullable()->change();
            $table->integer('status');
        });
    }
};
