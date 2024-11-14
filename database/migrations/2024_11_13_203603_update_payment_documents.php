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
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->dropForeign(['supply_id']);
            $table->dropForeign(['distributor_id']);
            $table->dropForeign(['trader_id']);
        });
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->boolean('manual')->default(false);
            $table->string('bank_purpose')->default('');
            $table->string('bank_number')->default('');
            $table->timestamp('bank_date')->nullable();
            $table->dropColumn('supply_id');
            $table->foreignId('recipient_id')->constrained('organizations')->onDelete('restrict');
            $table->foreignId('payer_id')->constrained('organizations')->onDelete('restrict');
            $table->string('recipient_account');
            $table->string('payer_account');

            $table->dropColumn('account');

            $table->dropColumn('distributor_id');
            $table->dropColumn('trader_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->dropForeign(['recipient_id']);
            $table->dropForeign(['payer_id']);
        });
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->dropColumn('manual');
            $table->dropColumn('bank_purpose');
            $table->dropColumn('bank_number');
            $table->dropColumn('bank_date');

            $table->dropColumn('recipient_id');
            $table->dropColumn('payer_id');
            $table->dropColumn('recipient_account');
            $table->dropColumn('payer_account');

            $table->string('account')->default('');
            $table->foreignId('supply_id')->nullable()->constrained('supply_documents')->onDelete('set null');
            $table->foreignId('distributor_id')->constrained('distributors')->onDelete('restrict');
            $table->foreignId('trader_id')->nullable()->constrained('traders')->onDelete('restrict');
        });


    }
};
