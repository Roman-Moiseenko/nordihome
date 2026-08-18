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
        Schema::table('widget_products', function (Blueprint $table) {
            $table->integer('modelable_id');
            $table->string('modelable_type');
            $table->string('button_name')->default('');
            $table->dropForeign(['banner_id']);
        });


        Schema::table('widget_products', function (Blueprint $table) {
            $table->dropColumn('banner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('widget_products', function (Blueprint $table) {
            $table->dropColumn('modelable_id');
            $table->dropColumn('modelable_type');
            $table->dropColumn('button_name');
        });

        Schema::table('widget_products', function (Blueprint $table) {
            $table->foreignId('banner_id')->nullable()->constrained('widget_banners')->onDelete('set null');
        });
    }
};
