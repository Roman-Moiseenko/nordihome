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
        Schema::table('widget_banner_items', function (Blueprint $table) {
            $table->dropForeign('banner_items_banner_id_foreign');
        });

        Schema::table('widget_banner_items', function (Blueprint $table) {
            $table->renameColumn('banner_id', 'widget_id');
        });

        Schema::table('widget_banner_items', function (Blueprint $table) {
            $table->foreign('widget_id')->references('id')->on('widget_banner_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('widget_banner_items', function (Blueprint $table) {
            $table->dropForeign(['widget_id']);
        });

        Schema::table('widget_banner_items', function (Blueprint $table) {
            $table->renameColumn('widget_id', 'banner_id');
        });

        Schema::table('widget_banner_items', function (Blueprint $table) {
            $table->foreign('banner_id', 'banner_items_banner_id_foreign')->references('id')->on('widget_banner_items')->onDelete('cascade');
        });


    }
};
