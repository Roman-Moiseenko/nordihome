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
        Schema::create('analytics_popular_searches', function (Blueprint $table) {
            $table->id();

            $table->string('query_normalized', 255)
                ->comment('Нормализованная поисковая строка');
            $table->string('query_sample', 255)
                ->comment('Пример исходного запроса (первое вхождение)');
            $table->unsignedInteger('searches_count')->default(0)
                ->comment('Общее количество поисков за период');
            $table->unsignedInteger('unique_visitors_count')->default(0)
                ->comment('Количество уникальных посетителей');
            $table->unsignedInteger('clicks_count')->default(0)
                ->comment('Количество кликов по результатам');
            $table->date('period_date')
                ->comment('Дата агрегации (или дата начала периода)');
            $table->string('period_type', 10)
                ->comment('day / week / month');

            $table->timestamp('updated_at')->nullable();

            // ============ ИНДЕКСЫ ============
            $table->unique(
                ['query_normalized', 'period_date', 'period_type'],
                'uq_popular_search_period'
            );
            $table->index('period_date', 'idx_popular_search_date');
            $table->index('period_type', 'idx_popular_search_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_popular_searches');
    }
};
