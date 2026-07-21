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
        Schema::table('feedback_forms', function (Blueprint $table) {
            $table->string('form_name');
            $table->renameColumn('data_form', 'data');
            $table->dropForeign(['widget_id']);
        });
        Schema::table('feedback_forms', function (Blueprint $table) {
            $table->dropColumn('widget_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback_forms', function (Blueprint $table) {
            $table->dropColumn('form_name');
            $table->renameColumn('data', 'data_form');
            $table->foreignId('widget_id')->constrained('widget_forms')->onDelete('cascade');
        });
    }
};
