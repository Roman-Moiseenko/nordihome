<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shopper_organizations', function (Blueprint $table) {
            $table->boolean('default')->default(false);
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');;
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shopper_organizations');
    }
};
