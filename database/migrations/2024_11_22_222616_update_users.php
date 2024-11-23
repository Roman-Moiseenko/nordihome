<?php

use App\Modules\User\Entity\User;
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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('active')->default(false);
        });
        $users = User::where('status', 'active')->get();
        foreach ($users as $user) {
            $user->active = true;
            $user->save();
        }
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unique('email');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->default('wait');
        });
        $users = User::where('active', true)->get();
        foreach ($users as $user) {
            $user->status = 'active';
            $user->save();
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }
};
