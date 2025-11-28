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
        Schema::create('widget_forms', function (Blueprint $table) {
            $table->id();
            Widget::columns($table);
            $table->json('fields');
            $table->json('lists');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_forms');
    }
};
