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
        Schema::table('parser_products', function (Blueprint $table) {
            $table->json('dimensions')->nullable();
            $table->json('variants')->nullable();
            $table->text('description')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parser_products', function (Blueprint $table) {
            $table->dropColumn('dimensions');
            $table->dropColumn('variants');
            $table->string('description')->default('')->nullable(false)->change();
        });
    }
};
