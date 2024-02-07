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
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('fullname_surname');
            $table->dropColumn('fullname_firstname');
            $table->dropColumn('fullname_secondname');
            $table->json('fullname');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('fullname_surname')->default('');
            $table->string('fullname_firstname')->default('');
            $table->string('fullname_secondname')->default('');
            $table->dropColumn('fullname');
        });
    }
};
