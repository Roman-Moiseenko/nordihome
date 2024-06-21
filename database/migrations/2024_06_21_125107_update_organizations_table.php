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
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('INN')->default('');
            $table->string('KPP')->default('');
            $table->string('OGRN')->default('');
            $table->string('BIK')->default('');
            $table->string('bank')->default('');
            $table->string('corr_account')->default('');
            $table->string('account')->default('');
            $table->string('email')->default('');
            $table->string('phone')->default('');
            $table->string('post_chief')->default('');

            $table->boolean('default')->default(false);

            $table->json('address');
            $table->json('chief');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('INN');
            $table->dropColumn('KPP');
            $table->dropColumn('OGRN');
            $table->dropColumn('BIK');
            $table->dropColumn('bank');
            $table->dropColumn('corr_account');
            $table->dropColumn('account');
            $table->dropColumn('email');
            $table->dropColumn('phone');
            $table->dropColumn('post_chief');

            $table->dropColumn('default');

            $table->dropColumn('address');
            $table->dropColumn('chief');

            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
};
