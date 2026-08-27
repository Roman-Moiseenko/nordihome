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
            $table->string('color_class')->default('red');
            $table->string('position_class')->default('top-right');
            $table->string('text_tag')->default('Акция');
            $table->boolean('show_tag')->default(true);
            $table->boolean('show_discount')->default(true);

            $table->string('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn('color_class');
            $table->dropColumn('position_class');
            $table->dropColumn('text_tag');
            $table->dropColumn('show_tag');
            $table->dropColumn('show_discount');
            $table->dropColumn('status');
        });
    }
};
