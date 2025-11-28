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
        Schema::create('feedback_forms', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->timestamp('created_at');
            $table->json('data_form');
            $table->foreignId('widget_id')->constrained('widget_forms')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_forms');
    }
};
