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
        Schema::create('analytics_page_views', function (Blueprint $table) {
            $table->id();

            // ============ СВЯЗИ ============
            $table->unsignedBigInteger('visitor_id')
                ->comment('Ссылка на analytics_visitors.id');
            $table->unsignedBigInteger('session_id')
                ->comment('Ссылка на analytics_sessions.id');

            // ============ СТРАНИЦА ============
            $table->string('page_type', 20)
                ->comment('Тип страницы: product, category, post, promo, page, search, home');
            $table->unsignedBigInteger('entity_id')->nullable()
                ->comment('ID сущности (товар, категория, запись, акция)');
            $table->text('url')
                ->comment('Полный URL страницы');
            $table->string('path', 2048)
                ->comment('Путь без домена');
            $table->string('title', 255)->nullable()
                ->comment('Заголовок страницы');
            $table->text('referrer')->nullable()
                ->comment('Referer на момент просмотра');

            // ============ МЕТРИКИ ============
            $table->timestamp('viewed_at')
                ->comment('Дата и время просмотра');
            $table->unsignedInteger('duration')->nullable()
                ->comment('Время на странице в секундах');
            $table->unsignedTinyInteger('scroll_depth')->nullable()
                ->comment('Максимальная глубина прокрутки в процентах (0–100)');

            // ============ ФЛАГИ ============
            $table->boolean('is_entry')->default(false)
                ->comment('Первая страница сессии');
            $table->boolean('is_exit')->default(false)
                ->comment('Последняя страница сессии');
            $table->boolean('is_bounce')->default(false)
                ->comment('Единственная страница в сессии');
            $table->boolean('is_bot')->default(false)
                ->comment('Просмотр от бота');

            $table->timestamp('created_at')->nullable();

            // ============ ИНДЕКСЫ ============
            $table->index('visitor_id', 'idx_pageview_visitor');
            $table->index('session_id', 'idx_pageview_session');
            $table->index('viewed_at', 'idx_pageview_viewed');
            $table->index('page_type', 'idx_pageview_page_type');
            $table->index('entity_id', 'idx_pageview_entity');
            $table->index(['session_id', 'viewed_at'], 'idx_pageview_session_viewed');

            $table->foreign('visitor_id', 'fk_pageview_visitor')
                ->references('id')
                ->on('analytics_visitors')
                ->cascadeOnDelete();

            $table->foreign('session_id', 'fk_pageview_session')
                ->references('id')
                ->on('analytics_sessions')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_page_views');
    }
};
