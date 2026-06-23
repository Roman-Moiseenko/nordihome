<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Старая таблица wish (без s) - может уже не существовать
        if (Schema::hasTable('wish')) {
            Schema::table('wish', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropIndex(['user_id', 'product_id']);
                $table->renameColumn('user_id', 'client_id');
                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
                $table->index(['client_id', 'product_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('wish')) {
            Schema::table('wish', function (Blueprint $table) {
                $table->dropForeign(['client_id']);
                $table->dropIndex(['client_id', 'product_id']);
                $table->renameColumn('client_id', 'user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['user_id', 'product_id']);
            });
        }
    }
};
