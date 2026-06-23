<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('responsibilities', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->renameColumn('admin_id', 'staff_id');
            $table->foreign('staff_id')->references('id')->on('staffs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('responsibilities', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->renameColumn('staff_id', 'admin_id');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }
};
