<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{


    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('code_search')->default('');
        });
        foreach (\App\Modules\Product\Entity\Product::orderBy('id')->get() as $product) {
            $product->update([
                'code_search' => str_replace(['-', ',', '.', '_'],'', $product->code),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('code_search');
        });
    }
};
