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
        Schema::rename('banners', 'widget_banners');
        Schema::rename('banner_items', 'widget_banner_items');


        Schema::rename('widgets', 'widget_products');
        Schema::rename('widget_items', 'widget_product_items');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('widget_banners', 'banners');
        Schema::rename('widget_banner_items', 'banner_items');

        Schema::rename('widget_products', 'widgets');
        Schema::rename('widget_product_items', 'widget_items');
    }
};
