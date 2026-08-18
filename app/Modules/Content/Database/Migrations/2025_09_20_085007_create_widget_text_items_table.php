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
        Schema::create('widget_text_items', function (Blueprint $table) {
            $table->id();
            $table->string('caption')->default('');
            $table->string('description')->default('');
            $table->integer('sort')->default(0);
            $table->string('slug')->nullable();

            $table->foreignId('widget_id')->constrained('widget_texts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_text_items');
    }
};
