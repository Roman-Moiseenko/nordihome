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
        Schema::create('feeds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('active');
            $table->json('products_in');
            $table->json('products_out');
            $table->json('categories_in');
            $table->json('categories_out');
            $table->json('tags_in');
            $table->json('tags_out');

            $table->boolean('set_preprice')->default(false);
            $table->string('set_title')->default('');
            $table->string('set_description')->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feeds');
    }
};
