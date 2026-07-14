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
        Schema::table('meta_templates', function (Blueprint $table) {
            $table->string('entity')->nullable(); //Сущность типа {модуль}.{сущность} catalog.product
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meta_templates', function (Blueprint $table) {
            $table->dropColumn('entity');
        });
    }
};
