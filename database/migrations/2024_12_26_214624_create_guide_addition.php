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
        Schema::create('guide_addition', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('base')->default(0);
            $table->boolean('manual')->default(true);
            $table->integer('type');
            $table->string('class')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_addition');
    }
};
