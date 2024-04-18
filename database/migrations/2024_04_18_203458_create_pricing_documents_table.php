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
        Schema::create('pricing_documents', function (Blueprint $table) {
            $table->id();
            $table->string('number')->default('');
            $table->boolean('completed')->default(false);
            $table->string('comment')->default('');
            $table->foreignId('arrival_id')->nullable()->constrained('arrival_documents')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('pricing_documents');
    }
};
