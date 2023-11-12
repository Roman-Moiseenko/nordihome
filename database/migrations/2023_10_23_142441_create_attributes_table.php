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
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('attribute_groups')->onDelete('restrict');
           // $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->string('name');

            $table->boolean('multiple')->default(false);
            $table->string('sameAs')->default('');
            $table->boolean('filter')->default(false);
            $table->integer('type')->index();
            $table->integer('sort')->default(0);
            $table->string('widget')->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
