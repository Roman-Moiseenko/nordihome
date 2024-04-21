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
        Schema::table('arrival_documents', function (Blueprint $table) {
            $table->foreignId('staff_id')->nullable()->constrained('admins')->onDelete('set null');
        });

        Schema::table('pricing_documents', function (Blueprint $table) {
            $table->foreignId('staff_id')->nullable()->constrained('admins')->onDelete('set null');
        });

        Schema::table('departure_documents', function (Blueprint $table) {
            $table->foreignId('staff_id')->nullable()->constrained('admins')->onDelete('set null');
        });

        Schema::table('movement_documents', function (Blueprint $table) {
            $table->foreignId('staff_id')->nullable()->constrained('admins')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arrival_documents', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropColumn('staff_id');
        });

        Schema::table('pricing_documents', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropColumn('staff_id');
        });
        Schema::table('departure_documents', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropColumn('staff_id');
        });
        Schema::table('movement_documents', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropColumn('staff_id');
        });
    }
};
