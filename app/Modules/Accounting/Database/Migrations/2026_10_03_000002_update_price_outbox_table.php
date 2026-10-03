<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('price_outbox', function (Blueprint $table) {
            $table->renameColumn('price', 'retail');
            $table->renameColumn('price_ikea', 'sell_ikea');
            $table->integer('bulk')->default(0)->after('sell_ikea');
        });
    }

    public function down(): void
    {
        Schema::table('price_outbox', function (Blueprint $table) {
            $table->dropColumn('bulk');
            $table->renameColumn('retail', 'price');
            $table->renameColumn('sell_ikea', 'price_ikea');
        });
    }
};
