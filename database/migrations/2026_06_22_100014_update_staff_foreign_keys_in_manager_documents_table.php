<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $tables = [
        'arrival_documents',
        'pricing_documents',
        'departure_documents',
        'movement_documents',
    ];

    /**
     * Проверяет, существует ли внешний ключ с заданным именем в таблице.
     */
    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        $result = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND CONSTRAINT_NAME = ?
        ", [$table, $constraintName]);

        return !empty($result);
    }

    /**
     * Безопасно удаляет внешний ключ, если он существует.
     */
    private function dropForeignKeyIfExists(string $table, string $constraintName): void
    {
        if ($this->foreignKeyExists($table, $constraintName)) {
            Schema::table($table, function (Blueprint $t) use ($constraintName) {
                $t->dropForeign($constraintName);
            });
        }
    }

    public function up(): void
    {
        // Отключаем проверки на время массовых изменений
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($this->tables as $table) {
            // 1. Удаляем старый FK на admins (если есть)
            $oldConstraint = $table . '_staff_id_foreign'; // например, arrival_documents_staff_id_foreign
            $this->dropForeignKeyIfExists($table, $oldConstraint);

            // 2. Удаляем возможный новый FK на staffs (если вдруг остался от предыдущих попыток)
            $newConstraint = $table . '_staff_id_foreign'; // имя то же самое, но мы удалим его, если есть
            // (на самом деле имя не изменилось, так как колонка та же, но ссылается на другую таблицу)
            // Чтобы избежать дублирования, удалим его тоже – потом создадим заново
            $this->dropForeignKeyIfExists($table, $newConstraint);
        }

        // 3. Удаляем записи, где staff_id отсутствует в staffs
        foreach ($this->tables as $table) {
            DB::table($table)
                ->whereNotIn('staff_id', DB::table('staffs')->select('id'))
                ->delete();
        }

        // 4. Создаём новые FK на staffs
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->foreign('staff_id')
                    ->references('id')
                    ->on('staffs')
                    ->onDelete('set null');
            });
        }

        // Включаем проверки обратно
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($this->tables as $table) {
            // 1. Удаляем новый FK на staffs (если есть)
            $newConstraint = $table . '_staff_id_foreign';
            $this->dropForeignKeyIfExists($table, $newConstraint);

            // 2. Удаляем старый FK на admins (если вдруг остался) – для чистоты
            $oldConstraint = $table . '_staff_id_foreign'; // имя то же
            $this->dropForeignKeyIfExists($table, $oldConstraint);
        }

        // 3. Удаляем записи, где staff_id отсутствует в admins (подготавливаем для старого FK)
        foreach ($this->tables as $table) {
            DB::table($table)
                ->whereNotIn('staff_id', DB::table('admins')->select('id'))
                ->delete();
        }

        // 4. Восстанавливаем старые FK на admins
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->foreign('staff_id')
                    ->references('id')
                    ->on('admins')
                    ->onDelete('set null');
            });
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
