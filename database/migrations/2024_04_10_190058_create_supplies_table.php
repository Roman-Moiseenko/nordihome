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
        Schema::create('supply_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('admins')->onDelete('cascade');
            $table->foreignId('distributor_id')->constrained('distributors')->onDelete('cascade');
            $table->integer('status');
            $table->string('comment')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('supply_documents');
    }
};
