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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->integer('region_code')->nullable();
            $table->boolean('is_pickup')->nullable();
            $table->text('comment_client')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('country');
            $table->dropColumn('region');
            $table->dropColumn('city');
            $table->dropColumn('street');
            $table->dropColumn('postal_code');
            $table->dropColumn('region_code');
            $table->dropColumn('is_pickup');
            $table->dropColumn('comment_client');
        });
    }
};
