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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('published')->default(false);
            $table->boolean('only_offline')->default(false);
            $table->boolean('preorder')->default(false);
            $table->boolean('not_delivery')->default(false);
            $table->boolean('not_local')->default(false);
            $table->dropColumn('status');
            $table->dropColumn('sell_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('published');
            $table->dropColumn('only_offline');
            $table->dropColumn('preorder');
            $table->dropColumn('not_delivery');
            $table->dropColumn('not_local');
            $table->string('status');
            $table->string('sell_method');
        });
    }
};
