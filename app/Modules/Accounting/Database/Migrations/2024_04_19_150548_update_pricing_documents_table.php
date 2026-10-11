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
        Schema::table('pricing_documents', function (Blueprint $table) {
            $table->dropColumn('number');
        });
        Schema::table('pricing_documents', function (Blueprint $table) {
            $table->integer('number')->nullable();
        });

        Schema::table('pricing_products', function (Blueprint $table) {
            $table->renameColumn('price_bunk', 'price_bulk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricing_documents', function (Blueprint $table) {
            $table->dropColumn('number');
        });
        Schema::table('pricing_documents', function (Blueprint $table) {
            $table->string('number')->default('');
        });
        Schema::table('pricing_products', function (Blueprint $table) {
            $table->renameColumn('price_bulk', 'price_bunk');
        });
    }
};
