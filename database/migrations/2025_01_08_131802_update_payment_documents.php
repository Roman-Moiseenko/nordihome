<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->json('bank_payment');

            $table->dropColumn('recipient_account');
            $table->dropColumn('payer_account');
            $table->dropColumn('bank_purpose');
            $table->dropColumn('bank_number');
            $table->dropColumn('bank_date');
        });
    }

    public function down(): void
    {
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->dropForeign('bank_payment');

            $table->string('recipient_account');
            $table->string('payer_account');
            $table->string('bank_purpose');
            $table->string('bank_number');
            $table->timestamp('bank_date');
        });
    }
};
