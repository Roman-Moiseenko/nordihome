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
        Schema::dropIfExists('setting_items');

        Schema::dropIfExists('settings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
        });


        Schema::create('setting_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setting_id')->constrained('settings')->onDelete('cascade');
            $table->string('name')->default('');
            $table->string('description')->default('');
            $table->string('tab')->default('common');
            $table->string('key');
            $table->json('value');
            $table->integer('type')->default(4);
            $table->unique(['setting_id', 'key']);
        });
    }
};
