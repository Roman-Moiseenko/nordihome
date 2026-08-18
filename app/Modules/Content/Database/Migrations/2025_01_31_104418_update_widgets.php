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
        Schema::table('widgets', function (Blueprint $table) {
            $table->dropColumn('published');

            $table->string('url')->default('');
            $table->string('caption')->default('');
            $table->string('description')->default('');

            $table->foreignId('banner_id')->nullable()->constrained('banners')->onDelete('set null');

            $table->dropColumn('data_class');
            $table->dropColumn('data_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('widgets', function (Blueprint $table) {
            $table->boolean('published')->default(false);

            $table->dropColumn('url');
            $table->dropColumn('caption');
            $table->dropColumn('description');

            $table->dropForeign(['banner_id']);

            $table->string('data_class')->default('');
            $table->integer('data_id')->nullable();
        });
        Schema::table('widgets', function (Blueprint $table) {
            $table->dropColumn('banner_id');
        });
    }
};
