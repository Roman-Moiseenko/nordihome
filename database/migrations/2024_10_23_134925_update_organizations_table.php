<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->foreignId('holding_id')->nullable()->constrained('organization_holdings')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['holding_id']);
        });
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('holding_id');
        });
    }
};
