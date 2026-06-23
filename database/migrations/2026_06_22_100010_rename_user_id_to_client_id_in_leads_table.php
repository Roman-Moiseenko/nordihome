<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // user_id мог быть удалён в более поздней миграции
            if (Schema::hasColumn('leads', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->renameColumn('user_id', 'client_id');
                $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'client_id')) {
                $table->dropForeign(['client_id']);
                $table->renameColumn('client_id', 'user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }
};
