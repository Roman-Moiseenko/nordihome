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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('price_type')->nullable();
            $table->float('discount')->nullable();
            $table->integer('region_code')->nullable();
            $table->boolean('is_pickup')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('price_type');
            $table->dropColumn('discount');
            $table->dropColumn('region_code');
            $table->dropColumn('is_pickup');
        });
    }
};
