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
        Schema::table('modifications_products', function (Blueprint $table) {
            $table->dropForeign(['modification_id']);
        });

        Schema::table('modifications_products', function (Blueprint $table) {
            $table->foreign('modification_id')->references('id')->on('modifications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modifications_products', function (Blueprint $table) {
            $table->dropForeign(['modification_id']);
        });

        Schema::table('modifications_products', function (Blueprint $table) {
            $table->foreign('modification_id')->references('id')->on('modifications')->onDelete('restrict');
        });
    }
};
