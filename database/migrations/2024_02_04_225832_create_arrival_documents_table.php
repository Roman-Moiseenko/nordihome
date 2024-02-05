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
        Schema::create('arrival_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distributor_id')->constrained('distributors')->onDelete('cascade');
            $table->foreignId('storage_id')->constrained('storages')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->string('number');
            $table->float('exchange_fix')->default(1.0);
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('arrival_documents');
    }
};
