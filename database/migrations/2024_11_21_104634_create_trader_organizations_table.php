<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trader_organizations', function (Blueprint $table) {
            $table->id();
            $table->boolean('default');
            $table->foreignId('trader_id')->constrained('traders')->onDelete('restrict');
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trader_organizations');
    }
};
