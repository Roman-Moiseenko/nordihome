<?php

use App\Modules\Guide\Entity\VAT;
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
        Schema::create('guide_v_a_t', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('value')->nullable();
        });
        VAT::create(['name' => 'Без НДС', 'value' => null]);
        VAT::create(['name' => 'НДС 0%', 'value' => 0]);
        VAT::create(['name' => 'НДС 5%', 'value' => 5]);
        VAT::create(['name' => 'НДС 7%', 'value' => 7]);
        VAT::create(['name' => 'НДС 10%', 'value' => 10]);
        VAT::create(['name' => 'НДС 20%', 'value' => 20]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_v_a_t');
    }
};
