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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->default('');
            $table->string('url')->default('');
            $table->json('sameas_json');
            $table->string('photo')->default('');
        });
        \App\Modules\Product\Entity\Brand::create(['name' => 'NONAME', 'description' => 'Default Brand']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
