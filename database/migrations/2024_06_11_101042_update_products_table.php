<?php

use App\Modules\Product\Entity\Product;
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
            $table->json('dimensions');
        });


        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('dimensions_json');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('dimensions_json');
        });


        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('dimensions');
        });
    }
};
