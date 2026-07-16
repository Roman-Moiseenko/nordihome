<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_blocks', function (Blueprint $table) {
            // Убираем каскадное удаление — удаление виджета не должно удалять блок
            $table->dropForeign(['widget_instance_id']);
            $table->foreign('widget_instance_id')
                ->references('id')
                ->on('widget_instances')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('content_blocks', function (Blueprint $table) {
            $table->dropForeign(['widget_instance_id']);
            $table->foreign('widget_instance_id')
                ->references('id')
                ->on('widget_instances')
                ->cascadeOnDelete();
        });
    }
};
