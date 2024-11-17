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
        Schema::table('arrival_documents', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
        });

        Schema::table('arrival_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('distributor_id')->nullable()->change();
            $table->foreign('distributor_id')->references('id')->on('distributors')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arrival_documents', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
        });

        Schema::table('arrival_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('distributor_id')->nullable(false)->change();
            $table->foreign('distributor_id')->references('id')->on('distributors')->onDelete('restrict');
        });
    }
};
