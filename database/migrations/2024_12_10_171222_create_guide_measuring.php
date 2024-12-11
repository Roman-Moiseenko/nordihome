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
            $table->string('name');
            $table->boolean('fractional')->default(false);
        });
        Measuring::create(['name' => 'шт']);
        Measuring::create(['name' => 'пачка']);
        Measuring::create(['name' => 'уп']);
        Measuring::create(['name' => 'кг', 'fractional' => true]);
        Measuring::create(['name' => 'м', 'fractional' => true]);
        Measuring::create(['name' => 'г']);
        Measuring::create(['name' => 'т', 'fractional' => true]);
        Measuring::create(['name' => 'км', 'fractional' => true]);
        Measuring::create(['name' => 'п.м.', 'fractional' => true]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_measuring');
    }
};
