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
        Schema::drop('delivery_statuses');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('delivery_orders')->onDelete('cascade');
            $table->integer('value');
            $table->timestamp('created_at');
            $table->string('comment')->default('');
        });
    }
};
