<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Шаг 1: переименовываем колонку (и удаляем старый FK, если есть)
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'user_id')) {
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Exception $e) {
                    // Ключа нет – игнорируем
                }
                $table->renameColumn('user_id', 'client_id');
            }
        });

        // Шаг 2: теперь колонка переименована, чистим данные (вне Schema::table)
        if (Schema::hasColumn('leads', 'client_id')) {
            // Удаляем записи, где client_id отсутствует в таблице clients
            DB::table('leads')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('clients')
                        ->whereRaw('clients.id = leads.client_id');
                })
                ->delete();

            // Шаг 3: добавляем новый внешний ключ
            Schema::table('leads', function (Blueprint $table) {
                $table->foreign('client_id')
                    ->references('id')
                    ->on('clients')
                    ->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        // Шаг 1: удаляем внешний ключ на client_id (если есть)
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'client_id')) {
                try {
                    $table->dropForeign(['client_id']);
                } catch (\Exception $e) {
                    // Игнорируем
                }
                // Также удаляем возможный ключ на user_id (если остался)
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Exception $e) {
                    // Игнорируем
                }
                $table->renameColumn('client_id', 'user_id');
            }
        });

        // Шаг 2: чистим записи, где user_id отсутствует в users
        if (Schema::hasColumn('leads', 'user_id')) {
            DB::table('leads')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('users')
                        ->whereRaw('users.id = leads.user_id');
                })
                ->delete();

            // Шаг 3: восстанавливаем внешний ключ на users, предварительно удалив его, если существует
            $fkExists = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'leads'
              AND CONSTRAINT_NAME = 'leads_user_id_foreign'
        ");
            if (!empty($fkExists)) {
                Schema::table('leads', function (Blueprint $table) {
                    $table->dropForeign('leads_user_id_foreign');
                });
            }

            Schema::table('leads', function (Blueprint $table) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
            });
        }
    }
};
