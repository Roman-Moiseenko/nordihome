<?php

namespace App\Modules\Order\Database\Seeders;

use App\Modules\Guide\Entity\Addition;
use App\Modules\Order\Entity\Addition\AssemblyCalculate;
use App\Modules\Order\Entity\Addition\DeliveryPolandCalculate;
use App\Modules\Order\Entity\Addition\PackingCalculate;
use Illuminate\Database\Seeder;

class AdditionSeeder extends Seeder
{
    public function run(): void
    {
        $array = [
            ['name' => 'Сборка мебели 15%', 'base' => 15, 'type' => 104, 'class' => AssemblyCalculate::class, 'slug' => 'assembly_15'],
            ['name' => 'Упаковка товара', 'base' => 1, 'type' => 103, 'class' => PackingCalculate::class, 'slug' => 'packing'],
            ['name' => 'Доставка из Польши', 'base' => 0, 'type' => 102, 'class' => DeliveryPolandCalculate::class, 'slug' => 'poland'],
            ['name' => 'Доставка в Россию', 'base' => 0, 'type' => 102, 'class' => null, 'slug' => 'russia', 'manual' => true],
            ['name' => 'Доставка по региону', 'base' => 0, 'type' => 102, 'class' => null, 'slug' => 'koenig', 'manual' => true],
        ];
        foreach ($array as $item) {
            if(is_null(Addition::where('slug', $item['slug'])->first())) {
                Addition::register(
                    name: $item['name'],
                    slug: $item['slug'],
                    type: $item['type'],
                    manual: $item['manual'] ?? false,
                    base: $item['base'],
                    class: $item['class'],
                    is_quantity: false,
                );
            }
        }
    }
}
