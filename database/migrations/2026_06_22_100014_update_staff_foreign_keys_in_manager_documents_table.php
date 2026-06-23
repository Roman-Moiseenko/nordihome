<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'arrival_documents',
        'pricing_documents',
        'departure_documents',
        'movement_documents',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropForeign(['staff_id']);
                $t->foreign('staff_id')->references('id')->on('staffs')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropForeign(['staff_id']);
                $t->foreign('staff_id')->references('id')->on('admins')->onDelete('set null');
            });
        }
    }
};
