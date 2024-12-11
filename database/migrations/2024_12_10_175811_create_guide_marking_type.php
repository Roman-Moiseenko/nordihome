<?php

use App\Modules\Guide\Entity\MarkingType;
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
        Schema::create('guide_marking_type', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('honest')->default(true);
        });
        MarkingType::create(['name' => 'Фототехника']);
        MarkingType::create(['name' => 'Одежда и другие товары лёгкой промышленности']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_marking_type');
    }
};
