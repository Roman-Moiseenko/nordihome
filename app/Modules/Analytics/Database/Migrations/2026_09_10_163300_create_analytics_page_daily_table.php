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
        Schema::create('analytics_page_daily', function (Blueprint $table) {
            $table->id();

            $table->date('date')
                ->comment('Дата агрегации');
            $table->string('page_type', 20)
                ->comment('Тип страницы: product, category, post, promo, page');
            $table->unsignedBigInteger('entity_id')->nullable()
                ->comment('ID сущности');
            $table->unsignedInteger('views_count')->default(0)
                ->comment('Количество просмотров за день');
            $table->unsignedInteger('unique_visitors_count')->default(0)
                ->comment('Уникальных посетителей');
            $table->unsignedInteger('avg_duration')->nullable()
                ->comment('Среднее время на странице в секундах');
            $table->unsignedInteger('bounce_count')->default(0)
                ->comment('Количество отказов');

            $table->timestamp('updated_at')->nullable();

            // ============ ИНДЕКСЫ ============
            $table->index(['date', 'page_type', 'entity_id'], 'idx_page_daily_key');
            $table->index('date', 'idx_page_daily_date');
            $table->index('page_type', 'idx_page_daily_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_page_daily');
    }
};
