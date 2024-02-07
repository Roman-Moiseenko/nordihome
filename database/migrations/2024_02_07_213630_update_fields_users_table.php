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
        Schema::table('user_deliveries', function (Blueprint $table) {
            $table->dropColumn('recipient_surname');
            $table->dropColumn('recipient_firstname');
            $table->dropColumn('recipient_secondname');
            $table->json('fullname');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_deliveries', function (Blueprint $table) {
            $table->string('recipient_surname')->default('');
            $table->string('recipient_firstname')->default('');
            $table->string('recipient_secondname')->default('');
            $table->dropColumn('fullname');
        });
    }
};
