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
        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('container_type', 50);               // 'page' или 'post'
            $table->unsignedBigInteger('container_id');
            $table->foreignId('widget_instance_id')->nullable()->constrained('widget_instances')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('section', 100)->nullable();         // 'header', 'body', 'sidebar' и т.п.
            $table->string('caption')->nullable();              // подпись блока для админки
            $table->timestamps();

            $table->index(['container_type', 'container_id', 'sort_order'], 'content_blocks_context_sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_blocks');
    }
};
