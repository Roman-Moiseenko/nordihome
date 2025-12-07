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
        Schema::create('parser_logs', function (Blueprint $table) {
            $table->id();
            $table->string('date');
            $table->foreignId('staff_id')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamp('read_at')->nullable();
            $table->boolean('read')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parser_logs');
    }
};
