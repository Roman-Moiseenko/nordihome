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
        Schema::create('analytics_paths', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('session_id')
                ->comment('Ссылка на analytics_sessions.id');
            $table->unsignedBigInteger('visitor_id')
                ->comment('Ссылка на analytics_visitors.id');
            $table->unsignedSmallInteger('step_number')
                ->comment('Порядковый номер шага в сессии');
            $table->string('page_type', 20)
                ->comment('Тип страницы');
            $table->unsignedBigInteger('entity_id')->nullable()
                ->comment('ID сущности');
            $table->text('url')
                ->comment('URL страницы');
            $table->unsignedInteger('duration')->nullable()
                ->comment('Время на странице');
            $table->string('action_type', 50)->nullable()
                ->comment('Действие, совершённое на этом шаге (если было)');
            $table->timestamp('occurred_at')
                ->comment('Время шага');

            // ============ ИНДЕКСЫ ============
            $table->index('session_id', 'idx_path_session');
            $table->index('visitor_id', 'idx_path_visitor');
            $table->index(['session_id', 'step_number'], 'idx_path_session_step');
            $table->index('occurred_at', 'idx_path_occurred');

            $table->foreign('session_id', 'fk_path_session')
                ->references('id')->on('analytics_sessions')->cascadeOnDelete();
            $table->foreign('visitor_id', 'fk_path_visitor')
                ->references('id')->on('analytics_visitors')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_paths');
    }
};
