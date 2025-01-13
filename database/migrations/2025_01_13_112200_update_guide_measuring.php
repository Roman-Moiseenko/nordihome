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
        Schema::table('guide_measuring', function (Blueprint $table) {
            $table->integer('code')->default(0);
            $table->string('fractional_name')->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guide_measuring', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('fractional_name');
        });
    }
};
