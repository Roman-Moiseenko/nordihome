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
        Schema::rename('log_activities', 'logger_activity');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('logger_activity', 'log_activities');
    }
};
