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
        Schema::create('analytics_sessions', function (Blueprint $table) {
            $table->id();

            // ============ СВЯЗЬ С ПОСЕТИТЕЛЕМ ============
            $table->unsignedBigInteger('visitor_id')
                ->comment('Ссылка на analytics_visitors.id');

            // ============ ВРЕМЯ СЕССИИ ============
            $table->timestamp('started_at')
                ->comment('Дата и время начала сессии');
            $table->timestamp('last_activity_at')
                ->comment('Последняя активность в сессии (обновляется при каждом просмотре/действии)');
            $table->timestamp('ended_at')->nullable()
                ->comment('Дата и время завершения сессии (по exit-запросу или по таймауту)');
            $table->unsignedInteger('duration')->nullable()
                ->comment('Длительность сессии в секундах');

            // ============ СЧЁТЧИКИ ============
            $table->unsignedInteger('page_views_count')->default(0)
                ->comment('Количество просмотренных страниц в сессии');
            $table->unsignedInteger('actions_count')->default(0)
                ->comment('Количество действий клиента в сессии');
            $table->unsignedInteger('searches_count')->default(0)
                ->comment('Количество поисковых запросов в сессии');

            // ============ СТРАНИЦЫ ============
            $table->text('entry_url')
                ->comment('URL первой страницы сессии');
            $table->string('entry_page_type', 20)
                ->comment('Тип первой страницы: product, category, post, promo, page, search, home');
            $table->unsignedBigInteger('entry_entity_id')->nullable()
                ->comment('ID сущности первой страницы (товар/категория/запись)');
            $table->text('exit_url')->nullable()
                ->comment('URL последней страницы сессии');
            $table->string('exit_page_type', 20)->nullable()
                ->comment('Тип последней страницы');
            $table->unsignedBigInteger('exit_entity_id')->nullable()
                ->comment('ID сущности последней страницы');

            // ============ ИСТОЧНИК ВХОДА ============
            $table->text('referrer')->nullable()
                ->comment('Referer входа в сессию');
            $table->string('source', 50)->nullable()
                ->comment('Классификация источника входа: direct, google, yandex, vk, telegram, internal, other');
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->string('utm_term', 100)->nullable();
            $table->string('utm_content', 100)->nullable();

            // ============ СРЕДА ============
            $table->string('ip', 45)->nullable()
                ->comment('IP-адрес сессии');
            $table->string('city', 100)->nullable()
                ->comment('Город по IP');
            $table->string('region', 100)->nullable()
                ->comment('Регион по IP');
            $table->string('country', 2)->nullable()
                ->comment('Код страны ISO');
            $table->string('user_agent', 512)->nullable()
                ->comment('User-Agent сессии');
            $table->string('device_type', 20)->nullable()
                ->comment('desktop / mobile / tablet / bot');
            $table->string('os', 50)->nullable()
                ->comment('Операционная система');
            $table->string('browser', 50)->nullable()
                ->comment('Браузер');

            // ============ СЛУЖЕБНЫЕ ============
            $table->boolean('is_bounce')->default(false)
                ->comment('Отказ: одна страница за сессию, без действий');
            $table->boolean('is_bot')->default(false)
                ->comment('Сессия от бота');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // ============ ИНДЕКСЫ ============
            $table->index('visitor_id', 'idx_session_visitor');
            $table->index('started_at', 'idx_session_started');
            $table->index('ended_at', 'idx_session_ended');
            $table->index('source', 'idx_session_source');

            $table->foreign('visitor_id', 'fk_session_visitor')
                ->references('id')
                ->on('analytics_visitors')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_sessions');
    }
};
