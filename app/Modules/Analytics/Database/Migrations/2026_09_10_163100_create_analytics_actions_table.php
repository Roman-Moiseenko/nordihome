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
        Schema::create('analytics_actions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('visitor_id')
                ->comment('Ссылка на analytics_visitors.id');
            $table->unsignedBigInteger('session_id')
                ->comment('Ссылка на analytics_sessions.id');
            $table->unsignedBigInteger('page_view_id')->nullable()
                ->comment('Ссылка на просмотр, в рамках которого совершено действие');

            $table->string('action_type', 50)
                ->comment('Тип действия: add_to_cart, remove_from_cart, set_quantity, add_to_wishlist, remove_from_wishlist, click_buy, form_submit, click_phone, click_email, share, ...');
            $table->string('entity_type', 20)->nullable()
                ->comment('Тип сущности: product, category, post, promo');
            $table->unsignedBigInteger('entity_id')->nullable()
                ->comment('ID сущности действия');
            $table->json('payload')->nullable()
                ->comment('Дополнительные данные (quantity, price, размер)');

            $table->timestamp('occurred_at')
                ->comment('Дата и время действия');
            $table->boolean('is_bot')->default(false)
                ->comment('Действие от бота');
            $table->timestamp('created_at')->nullable();

            // ============ ИНДЕКСЫ ============
            $table->index('visitor_id', 'idx_action_visitor');
            $table->index('session_id', 'idx_action_session');
            $table->index('page_view_id', 'idx_action_page_view');
            $table->index('action_type', 'idx_action_type');
            $table->index('occurred_at', 'idx_action_occurred');
            $table->index(['entity_type', 'entity_id'], 'idx_action_entity');

            $table->foreign('visitor_id', 'fk_action_visitor')
                ->references('id')->on('analytics_visitors')->cascadeOnDelete();
            $table->foreign('session_id', 'fk_action_session')
                ->references('id')->on('analytics_sessions')->cascadeOnDelete();
            $table->foreign('page_view_id', 'fk_action_page_view')
                ->references('id')->on('analytics_page_views')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_actions');
    }
};
