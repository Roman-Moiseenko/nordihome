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
        Schema::create('movement_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('storage_out')->constrained('storages')->onDelete('cascade');
            $table->foreignId('storage_in')->constrained('storages')->onDelete('cascade');
            $table->string('number')->nullable();
            $table->integer('order_id')->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('movement_documents');
    }
};
