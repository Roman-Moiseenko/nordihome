<?php

use App\Modules\Guide\Entity\Country;
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
        Schema::create('guide_country', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Country::create(['name' => 'Россия']);
        Country::create(['name' => 'Китай']);
        Country::create(['name' => 'Польша']);
        Country::create(['name' => 'Вьетнам']);
        Country::create(['name' => 'Тайланд']);
        Country::create(['name' => 'Индия']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_country');
    }
};
