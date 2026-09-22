<?php

namespace App\Modules\Guide\Database\Seeders;

use App\Modules\Guide\Entity\VAT;
use Illuminate\Database\Seeder;

class VATSeeder extends Seeder
{
    public function run(): void
    {
        $vats = [
            ['name' => 'Без НДС', 'value' => null],
            ['name' => 'НДС 0%', 'value' => 0],
            ['name' => 'НДС 5%', 'value' => 5],
            ['name' => 'НДС 7%', 'value' => 7],
            ['name' => 'НДС 10%', 'value' => 10],
            ['name' => 'НДС 20%', 'value' => 20],
        ];

        foreach ($vats as $vat) {
            VAT::firstOrCreate(
                ['name' => $vat['name']],
                ['value' => $vat['value']],
            );
        }
    }
}
