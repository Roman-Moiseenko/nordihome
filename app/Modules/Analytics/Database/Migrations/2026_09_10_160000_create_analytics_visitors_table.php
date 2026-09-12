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
        Schema::create('analytics_visitors', function (Blueprint $table) {
            $table->id();

            // ============ ИДЕНТИФИКАЦИЯ ============
            $table->uuid('uuid')->unique()
                ->comment('UUID из cookie, генерируется при первом заходе');
            $table->unsignedBigInteger('client_id')->nullable()
                ->comment('ID клиента из модуля Auth, появляется после логина');

            // ============ ВРЕМЕННЫЕ МЕТКИ ============
            $table->timestamp('first_visit_at')
                ->comment('Дата и время самого первого захода');
            $table->timestamp('last_visit_at')
                ->comment('Дата и время последнего захода');
            $table->unsignedInteger('visits_count')->default(1)
                ->comment('Общее количество визитов (сессий)');

            // ============ ДАННЫЕ ПЕРВОГО ВХОДА ============
            $table->string('ip', 45)->nullable()
                ->comment('IP первого захода (IPv4/IPv6)');
            $table->string('city', 100)->nullable()
                ->comment('Город первого захода (определяется по IP)');
            $table->string('region', 100)->nullable()
                ->comment('Регион первого захода');
            $table->string('country', 2)->nullable()
                ->comment('Код страны ISO 3166-1 alpha-2');
            $table->string('user_agent', 512)->nullable()
                ->comment('User-Agent первого захода');
            $table->string('device_type', 20)->nullable()
                ->comment('desktop / mobile / tablet / bot');
            $table->string('os', 50)->nullable()
                ->comment('Операционная система');
            $table->string('browser', 50)->nullable()
                ->comment('Браузер');

            // ============ ИСТОЧНИК ПЕРВОГО ВХОДА ============
            $table->text('referrer')->nullable()
                ->comment('Полный Referer URL первого захода');
            $table->string('source', 50)->nullable()
                ->comment('Классификация источника: direct, google, yandex, vk, telegram, ...');
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->string('utm_term', 100)->nullable();
            $table->string('utm_content', 100)->nullable();
            $table->string('landing_url', 2048)->nullable()
                ->comment('URL первой страницы, на которую попал посетитель');

            // ============ ПРИВЯЗКА К КЛИЕНТУ ============
            $table->timestamp('client_linked_at')->nullable()
                ->comment('Когда посетитель впервые авторизовался');

            // ============ СЛУЖЕБНЫЕ ============
            $table->boolean('is_bot')->default(false)
                ->comment('Запрос от поискового бота/парсера');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // ============ ИНДЕКСЫ ============
            $table->index('client_id', 'idx_visitor_client');
            $table->index('first_visit_at', 'idx_visitor_first_visit');
            $table->index('last_visit_at', 'idx_visitor_last_visit');
            $table->index('source', 'idx_visitor_source');
            $table->index(['uuid', 'client_id'], 'idx_visitor_uuid_client');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_visitors');
    }
};
