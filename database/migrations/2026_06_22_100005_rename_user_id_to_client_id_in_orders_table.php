<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Тут foreign key может быть с кастомным именем, сначала сбрасываем
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->renameColumn('user_id', 'client_id');
        });

        // Before deleting orphaned orders, we need to handle related records.
        // The chain is: orders → order_items → order_expense_items → batch_sales
        // batch_sales has ON DELETE RESTRICT on expense_item_id, so we must delete batch_sales first.

        // Get IDs of orders that have no matching client
        $orphanedOrderIds = DB::table('orders')
            ->leftJoin('clients', 'orders.client_id', '=', 'clients.id')
            ->whereNull('clients.id')
            ->pluck('orders.id');

        if ($orphanedOrderIds->isNotEmpty()) {
            // Delete batch_sales referencing expense_items of these orders
            DB::table('batch_sales')
                ->whereIn('expense_item_id', function ($query) use ($orphanedOrderIds) {
                    $query->select('order_expense_items.id')
                        ->from('order_expense_items')
                        ->join('order_items', 'order_expense_items.order_item_id', '=', 'order_items.id')
                        ->whereIn('order_items.order_id', $orphanedOrderIds);
                })
            ->delete();

            // Delete order_expense_items referencing these orders
            DB::table('order_expense_items')
                ->whereIn('order_item_id', function ($query) use ($orphanedOrderIds) {
                    $query->select('id')
                        ->from('order_items')
                        ->whereIn('order_id', $orphanedOrderIds);
                })
                ->delete();

            // Delete order_items referencing these orders
            DB::table('order_items')
                ->whereIn('order_id', $orphanedOrderIds)
                ->delete();

            // Now safely delete the orphaned orders
            DB::table('orders')
                ->whereIn('id', $orphanedOrderIds)
                ->delete();
    }

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        // 1. Проверяем, существует ли внешний ключ 'orders_client_id_foreign'
        $foreignKey = DB::select("
        SELECT CONSTRAINT_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'orders'
          AND CONSTRAINT_NAME = 'orders_client_id_foreign'
    ");

        if (!empty($foreignKey)) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign('orders_client_id_foreign');
            });
        }

        // 2. Также удаляем возможный старый ключ на user_id (если остался)
        $oldForeignKey = DB::select("
        SELECT CONSTRAINT_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'orders'
          AND CONSTRAINT_NAME = 'orders_user_id_foreign'
    ");

        if (!empty($oldForeignKey)) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign('orders_user_id_foreign');
            });
        }

        // 3. Переименовываем колонку обратно
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('client_id', 'user_id');
        });

        // 4. Удаляем записи, где user_id отсутствует в users (чтобы потом создать внешний ключ)
        DB::table('orders')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('users')
                    ->whereRaw('users.id = orders.user_id');
            })
            ->delete();

        // 5. Восстанавливаем внешний ключ на users
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });
        /*
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->renameColumn('client_id', 'user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });
        */
    }
};
