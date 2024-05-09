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
        Schema::create('delivery_trucks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('staff_id')->nullable()->constrained('admins')->onDelete('restrict');
            $table->float('weight')->default(0);
            $table->float('volume')->default(0);
            $table->boolean('cargo')->default(true);
            $table->boolean('service')->default(true);
            $table->boolean('active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('delivery_trucks');
    }
};
