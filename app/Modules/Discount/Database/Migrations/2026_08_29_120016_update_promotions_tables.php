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
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('description');
            $table->dropColumn('active');
            $table->dropColumn('published');

            $table->json('meta')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->string('title')->default('');
            $table->string('description')->default('');
            $table->boolean('active')->default(false);
            $table->boolean('published')->default(false);

            $table->dropColumn('meta');
        });
    }
};
