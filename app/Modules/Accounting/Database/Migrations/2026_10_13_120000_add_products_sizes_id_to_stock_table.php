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
        Schema::table('stock', function (Blueprint $table) {
            $table->foreignId('products_sizes_id')
                ->nullable()
                ->after('product_id')
                ->constrained('products_sizes')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock', function (Blueprint $table) {
            $table->dropConstrainedForeignId('products_sizes_id');
        });
    }
};
