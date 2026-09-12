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
        Schema::create('analytics_searches', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('visitor_id')
                ->comment('Ссылка на analytics_visitors.id');
            $table->unsignedBigInteger('session_id')->nullable()
                ->comment('Ссылка на analytics_sessions.id');
            $table->unsignedBigInteger('page_view_id')->nullable()
                ->comment('Ссылка на просмотр страницы, с которого сделан поиск');

            $table->string('query', 255)
                ->comment('Исходная поисковая строка');
            $table->string('query_normalized', 255)
                ->comment('Нормализованная строка (lower, trim, без стоп-слов) для агрегации');
            $table->unsignedInteger('results_count')->default(0)
                ->comment('Количество найденных результатов');
            $table->unsignedBigInteger('clicked_result_id')->nullable()
                ->comment('ID товара, по которому кликнули из результатов');
            $table->string('clicked_result_type', 20)->nullable()
                ->comment('Тип сущности результата: product, category, post');
            $table->unsignedSmallInteger('clicked_position')->nullable()
                ->comment('Позиция кликнутого результата в выдаче');

            $table->timestamp('searched_at')
                ->comment('Дата и время поиска');
            $table->boolean('is_bot')->default(false)
                ->comment('Поиск от бота');
            $table->timestamp('created_at')->nullable();

            // ============ ИНДЕКСЫ ============
            $table->index('visitor_id', 'idx_search_visitor');
            $table->index('session_id', 'idx_search_session');
            $table->index('page_view_id', 'idx_search_page_view');
            $table->index('query_normalized', 'idx_search_query');
            $table->index('searched_at', 'idx_search_searched');

            $table->foreign('visitor_id', 'fk_search_visitor')
                ->references('id')->on('analytics_visitors')->cascadeOnDelete();
            $table->foreign('session_id', 'fk_search_session')
                ->references('id')->on('analytics_sessions')->nullOnDelete();
            $table->foreign('page_view_id', 'fk_search_page_view')
                ->references('id')->on('analytics_page_views')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_searches');
    }
};
