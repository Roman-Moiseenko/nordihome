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
        $currency_default = \App\Modules\Accounting\Entity\Currency::first();
        Schema::table('distributors', function (Blueprint $table) use ($currency_default) {
            $table->unsignedBigInteger('currency_id')->default($currency_default->id);
        });
        Schema::table('distributors', function (Blueprint $table) use ($currency_default) {
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
        });
        Schema::table('distributors', function (Blueprint $table) {
            $table->dropColumn('currency_id');
        });
    }
};
