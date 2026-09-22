<?php

namespace App\Modules\Guide\Database\Seeders;

use App\Modules\Guide\Entity\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            'Россия',
            'Китай',
            'Польша',
            'Вьетнам',
            'Тайланд',
            'Индия',
        ];

        foreach ($countries as $name) {
            Country::firstOrCreate(['name' => $name]);
        }
    }
}
