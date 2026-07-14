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
        Schema::create('widget_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('widget_id')->constrained('widgets')->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('title')->nullable();
            $table->json('params')->default('{}');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_instances');
    }
};
