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
        Schema::create('composites', function (Blueprint $table) {
            $table->foreignId('parent_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('child_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('composites');
    }
};
