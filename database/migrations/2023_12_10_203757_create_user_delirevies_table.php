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
        Schema::create('user_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('recipient_surname')->default('');
            $table->string('recipient_firstname')->default('');
            $table->string('recipient_secondname')->default('');
            $table->json('local');
            $table->json('region');
            $table->string('company')->default('');
            $table->integer('storage')->nullable();
            $table->integer('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('user_deliveries');
    }
};
