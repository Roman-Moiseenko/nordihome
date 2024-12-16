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
        Schema::table('workers', function (Blueprint $table) {
            $table->integer('telegram_user_id')->nullable();
            $table->boolean('driver')->default(false);
            $table->boolean('loader')->default(false);
            $table->boolean('assemble')->default(false);
            $table->boolean('logistic')->default(false);

            $table->dropColumn('post');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropColumn('telegram_user_id');
            $table->dropColumn('driver');
            $table->dropColumn('loader');
            $table->dropColumn('assemble');
            $table->dropColumn('logistic');
            $table->integer('post')->nullable();

        });
    }
};
