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
        Schema::table('order_expenses', function (Blueprint $table) {
            $table->json('recipient');
            $table->string('phone')->default('');
            $table->integer('type')->nullable();
            $table->json('address');
            $table->foreignId('staff_id')->nullable()->constrained('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_expenses', function (Blueprint $table) {
            $table->dropColumn('recipient');
            $table->dropColumn('phone');
            $table->dropColumn('type');
            $table->dropColumn('address');
            $table->dropForeign(['staff_id']);
            $table->dropColumn('staff_id');
        });
    }
};
