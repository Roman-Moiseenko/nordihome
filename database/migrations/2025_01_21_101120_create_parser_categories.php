<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalnoy\Nestedset\NestedSet;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parser_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('');
            $table->string('url')->default('');
            $table->boolean('active')->default(true);
            $table->foreignId('brand_id')->nullable()->constrained('brands')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->cascadeOnDelete();
            NestedSet::columns($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parser_categories');
    }
};
