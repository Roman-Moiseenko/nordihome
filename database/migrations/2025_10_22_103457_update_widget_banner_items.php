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
        Schema::dropIfExists('widget_banner_items');

        Schema::create('widget_banner_items', function (Blueprint $table) {
            $table->id();
            $table->string('url')->default('');
            $table->string('caption')->default('');
            $table->string('description')->default('');
            $table->integer('sort')->default(0);
            $table->string('slug')->nullable();
            $table->foreignId('widget_id')->constrained('widget_banners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_banner_items');

        Schema::create('widget_banner_items', function (Blueprint $table) {
            $table->id();
            $table->string('url')->default('');
            $table->string('caption')->default('');
            $table->string('description')->default('');
            $table->integer('sort')->default(0);
            $table->string('slug')->nullable();
            $table->foreignId('widget_id')->constrained('widget_banners')->onDelete('cascade');
        });
    }
};
