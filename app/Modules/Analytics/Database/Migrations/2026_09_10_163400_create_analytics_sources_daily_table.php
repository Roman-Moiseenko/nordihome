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
        Schema::create('analytics_sources_daily', function (Blueprint $table) {
            $table->id();

            $table->date('date')
                ->comment('Дата агрегации');
            $table->string('source', 50)
                ->comment('Источник: direct, google, yandex, vk, telegram, internal, other');
            $table->string('utm_source', 100)->nullable()
                ->comment('UTM Source');
            $table->string('utm_medium', 100)->nullable()
                ->comment('UTM Medium');
            $table->string('utm_campaign', 100)->nullable()
                ->comment('UTM Campaign');
            $table->unsignedInteger('sessions_count')->default(0)
                ->comment('Количество сессий');
            $table->unsignedInteger('unique_visitors_count')->default(0)
                ->comment('Уникальных посетителей');
            $table->unsignedInteger('new_visitors_count')->default(0)
                ->comment('Новых посетителей');
            $table->unsignedInteger('bounce_count')->default(0)
                ->comment('Отказов');
            $table->unsignedInteger('avg_duration')->nullable()
                ->comment('Средняя длительность сессии');

            $table->timestamp('updated_at')->nullable();

            // ============ ИНДЕКСЫ ============
            $table->index(['date', 'source'], 'idx_source_daily_date_source');
            $table->index('utm_source', 'idx_source_daily_utm_source');
            $table->index('utm_medium', 'idx_source_daily_utm_medium');
            $table->index('utm_campaign', 'idx_source_daily_utm_campaign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_sources_daily');
    }
};
