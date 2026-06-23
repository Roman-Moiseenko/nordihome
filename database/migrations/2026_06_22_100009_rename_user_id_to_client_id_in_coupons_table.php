<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // coupons.user_id нет foreign key, просто rename
        Schema::table('coupons', function (Blueprint $table) {
            $table->renameColumn('user_id', 'client_id');
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->renameColumn('client_id', 'user_id');
        });
    }
};
