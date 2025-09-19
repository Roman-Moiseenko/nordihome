<?php

use App\Modules\Page\Entity\Widget;
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
        Schema::create('widget_promotions', function (Blueprint $table) {
            $table->id();
            Widget::columns($table);
            $table->foreignId('banner_id')->nullable()->constrained('banners')->onDelete('set null');
            $table->foreignId('promotion_id')->nullable()->constrained('promotions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_promotions');
    }
};
