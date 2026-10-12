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
        Schema::create('size_grids', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('dimensions');
        });

        Schema::create('size_grid_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('size_grid_id')
                ->constrained('size_grids')
                ->cascadeOnDelete();
            $table->string('dimension_code');
            $table->string('value');
            $table->string('label')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->unique(['size_grid_id', 'dimension_code', 'value']);
            $table->index(['size_grid_id', 'dimension_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('size_grid_values');
        Schema::dropIfExists('size_grids');
    }
};
