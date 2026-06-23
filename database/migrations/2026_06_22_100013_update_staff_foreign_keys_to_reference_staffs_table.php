<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'order_responsible' => ['staff_id', 'cascade'],
        'logger_orders' => ['staff_id', 'cascade'],
        'supply_stack' => ['staff_id', 'cascade'],
        'order_expense_refunds' => ['staff_id', 'restrict'],
        'lead_items' => ['staff_id', 'cascade'],
        'parser_logs' => ['staff_id', 'set null'],
        'leads' => ['staff_id', 'set null'],
        'order_expenses' => ['staff_id', 'set null'],
        'payment_documents' => ['staff_id', 'restrict'],
        'orders' => ['staff_id', 'restrict'],
    ];

    public function up(): void
    {
        foreach ($this->tables as $table => [$column, $onDelete]) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            Schema::table($table, function (Blueprint $t) use ($table, $column, $onDelete) {
                try { $t->dropForeign([$column]); } catch (\Exception $e) {}
                $t->foreign($column)->references('id')->on('staffs')->onDelete($onDelete);
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table => [$column, $onDelete]) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            Schema::table($table, function (Blueprint $t) use ($column, $onDelete) {
                try { $t->dropForeign([$column]); } catch (\Exception $e) {}
                $t->foreign($column)->references('id')->on('admins')->onDelete($onDelete);
            });
        }
    }
};
