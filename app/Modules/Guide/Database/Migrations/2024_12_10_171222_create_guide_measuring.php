<?php

use App\Modules\Guide\Entity\Measuring;
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
        Schema::create('guide_measuring', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('fractional')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_measuring');
    }
};
