<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->decimal('ratio_weight', 10, 2);
            $table->decimal('ratio_markup', 6, 3);
            $table->unsignedInteger('rounding_step')->default(100);
            $table->unsignedInteger('rounding_subtract')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};
