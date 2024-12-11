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
            $table->foreignId('vat_id')->nullable()->constrained('guide_v_a_t')->onDelete('set null');
            $table->foreignId('country_id')->nullable()->constrained('guide_country')->onDelete('set null');
            $table->foreignId('measuring_id')->nullable()->constrained('guide_measuring')->onDelete('set null');
            $table->foreignId('marking_type_id')->nullable()->constrained('guide_marking_type')->onDelete('set null');
            $table->boolean('hide_price')->default(true);
            $table->string('comment')->default('');
            $table->string('name_print')->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['vat_id']);
            $table->dropForeign(['country_id']);
            $table->dropForeign(['measuring_id']);
            $table->dropForeign(['marking_type_id']);

            $table->dropColumn('hide_price');
            $table->dropColumn('comment');
            $table->dropColumn('name_print');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('vat_id');
            $table->dropColumn('country_id');
            $table->dropColumn('measuring_id');
            $table->dropColumn('marking_type_id');
        });
    }
};
