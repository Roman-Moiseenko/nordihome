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
        Schema::dropIfExists('product_prices_bulk');
        Schema::dropIfExists('product_prices_cost');
        Schema::dropIfExists('product_prices_min');
        Schema::dropIfExists('product_prices_pre');
        Schema::dropIfExists('product_prices_retail');
        Schema::dropIfExists('product_prices_special');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
