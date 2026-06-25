<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shopper_organizations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->renameColumn('user_id', 'client_id');
        });

        // Удаляем записи, где client_id нет в clients — иначе FK (restrict) не даст создать
        DB::table('shopper_organizations')
            ->whereNotIn('client_id', DB::table('clients')->select('id'))
            ->delete();

        Schema::table('shopper_organizations', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('shopper_organizations', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->renameColumn('client_id', 'user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }
};
