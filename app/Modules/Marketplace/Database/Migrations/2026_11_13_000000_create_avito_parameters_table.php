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
        Schema::create('avito_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Английское название поля (ключ сопоставления с реестром параметров).
            $table->string('name_en')->nullable()->unique();
            $table->string('slug')->unique();
            $table->text('value')->nullable();
            $table->json('variants')->nullable();
            $table->boolean('is_html')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avito_parameters');
    }
};
