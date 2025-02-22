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
        Schema::table('system_mails', function (Blueprint $table) {
            $table->unsignedBigInteger('systemable_id')->nullable()->change();
            $table->string('systemable_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_mails', function (Blueprint $table) {
            $table->unsignedBigInteger('systemable_id')->change();
            $table->string('systemable_type')->change();
        });
    }
};
