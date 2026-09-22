<?php

namespace App\Modules\Guide\Database\Seeders;

use App\Modules\Guide\Entity\Measuring;
use Illuminate\Database\Seeder;

class MeasuringSeeder extends Seeder
{
    public function run(): void
    {
        $measurings = [
            ['name' => 'шт', 'code' => '795'],
            ['name' => 'пачка', 'code' => '728'],
            ['name' => 'уп', 'code' => '778'],
            ['name' => 'кг', 'code' => '166', 'fractional' => true, 'fractional_name' => 'г'],
            ['name' => 'м', 'code' => '006', 'fractional' => true, 'fractional_name' => 'мм'],
            ['name' => 'г', 'code' => '163'],
            ['name' => 'т', 'code' => '534', 'fractional' => true, 'fractional_name' => 'кг'],
            ['name' => 'км', 'code' => '008', 'fractional' => true, 'fractional_name' => 'м'],
            ['name' => 'п.м.', 'code' => '018'],
        ];

        foreach ($measurings as $measuring) {
            Measuring::firstOrCreate(
                ['name' => $measuring['name']],
                [
                    'code' => $measuring['code'],
                    'fractional' => $measuring['fractional'] ?? false,
                    'fractional_name' => $measuring['fractional_name'] ?? '',
                ],
            );
        }
    }
}
