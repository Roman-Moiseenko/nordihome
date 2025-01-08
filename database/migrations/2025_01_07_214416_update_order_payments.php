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
        Schema::table('order_payments', function (Blueprint $table) {
            $table->json('bank_payment');
            $table->foreignId('storage_id')->nullable()->constrained('storages')->onDelete('restrict');
            $table->boolean('manual')->default(false);
            $table->decimal('commission', 10, 2);
            $table->integer('method')->nullable()->change();
            $table->renameColumn('document', 'comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_payments', function (Blueprint $table) {
            $table->dropForeign(['storage_id']);
        });
        Schema::table('order_payments', function (Blueprint $table) {
            $table->dropColumn('bank_payment');
            $table->dropColumn('storage_id');
            $table->dropColumn('manual');
            $table->dropColumn('commission');
            $table->string('method')->default('')->change();
            $table->renameColumn('comment', 'document');
        });
    }
};
