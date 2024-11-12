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
        Schema::table('movement_documents', function (Blueprint $table) {
            $table->string('incoming_number')->default('');
            $table->timestamp('incoming_at')->nullable();
            $table->string('number')->default('')->change();
            $table->boolean('completed')->default(false);
            $table->foreignId('arrival_id')->nullable()->constrained('arrival_documents')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movement_documents', function (Blueprint $table) {
            $table->dropColumn('incoming_number');
            $table->dropColumn('incoming_at');
            $table->integer('number')->default(0)->change();
            $table->dropColumn('completed');
            $table->dropForeign(['arrival_id']);
        });
        Schema::table('movement_documents', function (Blueprint $table) {
            $table->dropColumn('arrival_id');
        });
    }
};
