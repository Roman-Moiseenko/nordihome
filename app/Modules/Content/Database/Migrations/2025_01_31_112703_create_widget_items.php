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
        Schema::create('widget_items', function (Blueprint $table) {
            $table->id();
            $table->string('url')->default('');
            $table->string('caption')->default('');
            $table->string('description')->default('');
            $table->string('slug')->nullable();
            $table->integer('sort');
            $table->foreignId('widget_id')->constrained('widgets')->onDelete('cascade');
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_items');
    }
};
