<?php

use App\Modules\Content\Entity\Widgets\WidgetItem;
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
        Schema::create('widget_catalog_items', function (Blueprint $table) {
            WidgetItem::columns($table, 'widget_catalogs');
            $table->integer('model_id');
            $table->string('model_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_catalog_items');
    }
};
