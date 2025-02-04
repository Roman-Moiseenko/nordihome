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
        Schema::table('delivery_trucks', function (Blueprint $table) {
            $table->dropColumn('service');
            $table->dropColumn('cargo');
            $table->dropForeign(['worker_id']);
        });

        Schema::table('delivery_trucks', function (Blueprint $table) {
            $table->dropColumn('worker_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_trucks', function (Blueprint $table) {
            $table->boolean('service')->default(true);
            $table->boolean('cargo')->default(true);
            $table->foreignId('worker_id')->nullable()->constrained('workers')->onDelete('set null');
        });
    }
};
