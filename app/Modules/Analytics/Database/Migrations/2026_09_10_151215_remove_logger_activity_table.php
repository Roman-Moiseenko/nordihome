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
        Schema::dropIfExists('logger_activity');
        Schema::dropIfExists('logger_cron_items');
        Schema::dropIfExists('logger_cron');



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('logger_activity', function (Blueprint $table) {
            $table->id();
        });

        Schema::create('logger_cron', function (Blueprint $table) {
            $table->id();
        });

        Schema::create('logger_cron_items', function (Blueprint $table) {
            $table->id();
        });
    }
};
