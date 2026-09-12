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
        Schema::create('analytics_exits', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('visitor_id')
                ->comment('Ссылка на analytics_visitors.id');
            $table->unsignedBigInteger('session_id')
                ->comment('Ссылка на analytics_sessions.id');
            $table->unsignedBigInteger('page_view_id')
                ->comment('Ссылка на последний просмотр');
            $table->text('url')
                ->comment('URL выхода');
            $table->string('page_type', 20)
                ->comment('Тип страницы выхода');
            $table->unsignedBigInteger('entity_id')->nullable()
                ->comment('ID сущности');
            $table->timestamp('exit_at')
                ->comment('Время выхода');
            $table->unsignedInteger('duration_on_page')
                ->comment('Время на последней странице');
            $table->string('reason', 20)->nullable()
                ->comment('beacon / timeout / navigation');

            // ============ ИНДЕКСЫ ============
            $table->index('visitor_id', 'idx_exit_visitor');
            $table->index('session_id', 'idx_exit_session');
            $table->index('page_view_id', 'idx_exit_page_view');
            $table->index('exit_at', 'idx_exit_at');

            $table->foreign('visitor_id', 'fk_exit_visitor')
                ->references('id')->on('analytics_visitors')->cascadeOnDelete();
            $table->foreign('session_id', 'fk_exit_session')
                ->references('id')->on('analytics_sessions')->cascadeOnDelete();
            $table->foreign('page_view_id', 'fk_exit_page_view')
                ->references('id')->on('analytics_page_views')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_exits');
    }
};
