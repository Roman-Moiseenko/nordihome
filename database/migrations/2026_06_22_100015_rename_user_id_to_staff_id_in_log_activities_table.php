<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('logger_activity')) {
            Schema::table('logger_activity', function (Blueprint $table) {
            $table->renameColumn('user_id', 'staff_id');
        });
    }
    }

    public function down(): void
    {
        if (Schema::hasTable('logger_activity')) {
            Schema::table('logger_activity', function (Blueprint $table) {
            $table->renameColumn('staff_id', 'user_id');
        });
    }
    }
};
