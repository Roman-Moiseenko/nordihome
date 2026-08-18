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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('slug');
            $table->string('title');
            $table->string('condition_url')->default('');
            $table->boolean('menu')->default(false);
            $table->boolean('show_title')->default(false);
            $table->boolean('published')->default(false);
            $table->boolean('active')->default(false);
            $table->date('start_at')->nullable(); //timestamp
            $table->date('finish_at')->nullable(); //timestamp ??
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('promotions');
    }
};
