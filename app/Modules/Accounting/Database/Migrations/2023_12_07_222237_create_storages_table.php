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
        Schema::create('storages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('restrict');
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
            $table->string('post')->default('');
            $table->string('address')->default('');
            $table->boolean('point_of_sale');
            $table->boolean('point_of_delivery');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('storages');
    }
};
