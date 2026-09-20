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
        Schema::table('feeds', function (Blueprint $table) {
            $table->json('rooms_in')->nullable();
            $table->json('rooms_out')->nullable();
            $table->json('promotions_in')->nullable();
            $table->json('promotions_out')->nullable();
            $table->json('groups_in')->nullable();
            $table->json('groups_out')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feeds', function (Blueprint $table) {
            $table->dropColumn('rooms_in');
            $table->dropColumn('rooms_out');
            $table->dropColumn('promotions_in');
            $table->dropColumn('promotions_out');
            $table->dropColumn('groups_in');
            $table->dropColumn('groups_out');
        });
    }
};
