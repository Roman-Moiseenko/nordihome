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
      /*  Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
        });*/
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('manager_id', 'staff_id');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('staff_id')->references('id')->on('admins')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('staff_id', 'manager_id');
        });
    }
};
