<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reserve', function (Blueprint $table) {
            $table->index('user_id', 'reserve_user_id_foreign'); //['user_id', 'product_id']
        });
        Schema::table('reserve', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'product_id']); //['user_id', 'product_id']
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reserve', function (Blueprint $table) {
            $table->unique(['user_id', 'product_id']);
        });
    }
};
