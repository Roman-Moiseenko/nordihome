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
        Schema::table('parser_log_items', function (Blueprint $table) {
            $table->string('status')->change();
            $table->string('error')->nullable();
            $table->renameColumn('data', 'payload');
        });
        Schema::table('parser_log_items', function (Blueprint $table) {
            $table->foreignId('parser_id')->nullable()->change();
            $table->json('payload')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parser_log_items', function (Blueprint $table) {
            $table->foreignId('parser_id')->nullable(false)->change();
            $table->json('payload')->nullable(false)->change();
        });

        Schema::table('parser_log_items', function (Blueprint $table) {
            $table->integer('status')->change();
            $table->dropColumn('error');
            $table->renameColumn('payload', 'data');
        });

    }
};
