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
        Schema::create('promotions_groups', function (Blueprint $table) {
            $table->foreignId('promotion_id')->constrained('promotions')->onDelete('restrict');
            $table->foreignId('group_id')->constrained('groups')->onDelete('restrict');
            $table->integer('discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('promotions_groups');
    }
};
