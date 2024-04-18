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
            $table->float('current_price')->default(0);
        });

        $products = Product::get();
        /** @var Product $product */
        foreach ($products as $product) {
            if (!is_null($product->priceRetail)) {
                $product->current_price = $product->priceRetail->value;
                $product->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('current_price');
        });
    }
};
