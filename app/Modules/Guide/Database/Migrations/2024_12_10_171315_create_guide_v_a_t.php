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
            $table->string('name')->unique();
            $table->integer('value')->nullable();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_v_a_t');
    }
};
