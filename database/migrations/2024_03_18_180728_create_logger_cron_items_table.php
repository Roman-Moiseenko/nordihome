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
        Schema::create('logger_cron_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('logger_id')->constrained('logger_cron')->onDelete('cascade');
            $table->string('object')->default('');
            $table->string('action')->default('');
            $table->string('value')->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('logger_cron_items');
    }
};
