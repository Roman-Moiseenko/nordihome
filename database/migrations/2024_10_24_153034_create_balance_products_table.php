<?php

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('balance_products', function (Blueprint $table) {
            $table->id();
            $table->integer('min');
            $table->boolean('buy');
            $table->foreignIdFor(Product::class)->constrained('products')->onDelete('cascade');
            $table->integer('max')->nullable();
        });


    }

    public function down(): void
    {
        Schema::dropIfExists('balance_products');
    }
};
