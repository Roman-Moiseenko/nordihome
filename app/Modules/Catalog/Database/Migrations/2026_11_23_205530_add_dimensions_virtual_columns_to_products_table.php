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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('height', 10, 2)->nullable()->virtualAs("dimensions->>'$.height'");
            $table->decimal('width', 10, 2)->nullable()->virtualAs("dimensions->>'$.width'");
            $table->decimal('depth', 10, 2)->nullable()->virtualAs("dimensions->>'$.depth'");

            $table->index('height', 'idx_products_height');
            $table->index('width', 'idx_products_width');
            $table->index('depth', 'idx_products_depth');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_height');
            $table->dropIndex('idx_products_width');
            $table->dropIndex('idx_products_depth');

            $table->dropColumn(['height', 'width', 'depth']);
        });
    }
};
