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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->unique();

            $table->text('description')->nullable();
            $table->text('short')->nullable();

            //$table->unsignedBigInteger('main_photo_id')->nullable();
            $table->foreignId('main_category_id')->constrained('categories')->onDelete('cascade');

            $table->json('dimensions_json');
            $table->json('frequency_json');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');

            $table->decimal('current_rating',2, 1, true);
            $table->decimal('count_for_sell',10, 1, true);
            $table->string('status');
            $table->string('sell_method');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
