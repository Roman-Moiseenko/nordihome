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
            $table->renameColumn('name', 'full_name');
            $table->renameColumn('bank', 'bank_name');
            $table->renameColumn('address', 'legal_address');
            $table->json('actual_address');
            $table->renameColumn('INN', 'inn');
            $table->renameColumn('KPP', 'kpp');
            $table->renameColumn('OGRN', 'ogrn');
            $table->renameColumn('BIK', 'bik');
            $table->renameColumn('post_chief', 'post');
            $table->renameColumn('account', 'pay_account');
            $table->boolean('active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->renameColumn('full_name', 'name');
            $table->renameColumn('bank_name', 'bank');
            $table->renameColumn('legal_address', 'address');
            $table->dropColumn('actual_address');
            $table->renameColumn('inn', 'INN');
            $table->renameColumn('kpp','KPP');
            $table->renameColumn('ogrn', 'OGRN');
            $table->renameColumn('bik', 'BIK');
            $table->renameColumn('post', 'post_chief');
            $table->renameColumn('pay_account', 'account');
            $table->dropColumn('active');
        });
    }
};
