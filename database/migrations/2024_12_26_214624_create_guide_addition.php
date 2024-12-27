<?php

use App\Modules\Guide\Entity\Addition;
use App\Modules\Order\Entity\Addition\AssemblyCalculate;
use App\Modules\Order\Entity\Addition\DeliveryPolandCalculate;
use App\Modules\Order\Entity\Addition\LiftingCalculate;
use App\Modules\Order\Entity\Addition\PackingCalculate;
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
        Schema::create('guide_addition', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('base')->default(0);
            $table->boolean('manual')->default(true);
            $table->integer('type');
            $table->string('class')->nullable();
        });
        Addition::register('Сборка мебели 15%', Addition::ASSEMBLY, false, 15, AssemblyCalculate::class);
        Addition::register('Упаковка товара', Addition::PACKING, false, 1, PackingCalculate::class);
        Addition::register('Подъем на 1 этаж, лестница', Addition::LIFTING, false, 2, LiftingCalculate::class);
        Addition::register('Подъем на 1 этаж, лифт', Addition::LIFTING, false, 1, LiftingCalculate::class);
        Addition::register('Доставка из Польши', Addition::DELIVERY, false, 15, DeliveryPolandCalculate::class);
        Addition::register('Доставка по городу', Addition::DELIVERY, false, 500);
        Addition::register('Доставка по региону', Addition::DELIVERY, true, 0);
        Addition::register('Доставка по России', Addition::DELIVERY, true, 0);


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_addition');
    }
};
