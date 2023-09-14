<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('fullname_surname', 33)->default('');
            $table->string('fullname_firstname', 33)->default('');
            $table->string('fullname_secondname', 33)->default('');
            $table->string('post', 33)->default('');
            $table->string('photo')->nullable();
            $table->boolean('active')->default(true);
        });

        DB::table('admins')->update([
            'active' => true
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('fullname_surname');
            $table->dropColumn('fullname_firstname');
            $table->dropColumn('fullname_secondname');
            $table->dropColumn('photo');
            $table->dropColumn('post');
            $table->dropColumn('active');
        });
    }
};
