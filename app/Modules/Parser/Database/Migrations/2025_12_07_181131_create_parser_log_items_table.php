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
        Schema::create('parser_log_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('log_id')->constrained('parser_logs')->onDelete('cascade');
            $table->foreignId('parser_id')->constrained('parser_products')->onDelete('cascade');
            $table->integer('status');
            $table->json('data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parser_log_items');
    }
};
