<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private string $table = 'arrival_expense_documents';

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
        $constraint = $this->table . '_staff_id_foreign';

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // 1. Удаляем старый FK на admins
        $this->dropForeignKeyIfExists($this->table, $constraint);

        // 2. Удаляем записи, где staff_id отсутствует в staffs
        DB::table($this->table)
            ->whereNotIn('staff_id', DB::table('staffs')->select('id'))
            ->delete();

        // 3. Создаём новый FK на staffs
        Schema::table($this->table, function (Blueprint $t) {
            $t->foreign('staff_id')
                ->references('id')
                ->on('staffs')
                ->onDelete('restrict');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        $constraint = $this->table . '_staff_id_foreign';

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // 1. Удаляем новый FK на staffs
        $this->dropForeignKeyIfExists($this->table, $constraint);

        // 2. Удаляем записи, где staff_id отсутствует в admins
        DB::table($this->table)
            ->whereNotIn('staff_id', DB::table('admins')->select('id'))
            ->delete();

        // 3. Восстанавливаем FK на admins
        Schema::table($this->table, function (Blueprint $t) {
            $t->foreign('staff_id')
                ->references('id')
                ->on('admins')
                ->onDelete('restrict');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
