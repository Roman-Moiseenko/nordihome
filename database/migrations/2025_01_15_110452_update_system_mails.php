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
            $table->json('emails');
            $table->unsignedBigInteger('systemable_id');
            $table->string('systemable_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_mails', function (Blueprint $table) {
            $table->dropColumn('emails');
            $table->dropColumn('systemable_id');
            $table->dropColumn('systemable_type');
        });
    }
};
