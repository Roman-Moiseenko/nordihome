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
        Schema::table('arrival_products', function (Blueprint $table) {
            $table->decimal('remains', 10, 3)->default(0.000)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arrival_products', function (Blueprint $table) {
            $table->integer('remains')->default(0)->change();
        });
    }
};
