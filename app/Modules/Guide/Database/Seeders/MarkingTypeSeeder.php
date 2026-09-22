<?php

namespace App\Modules\Guide\Database\Seeders;

use App\Modules\Guide\Entity\MarkingType;
use Illuminate\Database\Seeder;

class MarkingTypeSeeder extends Seeder
{
    public function run(): void
    {
        $markingTypes = [
            'Фототехника',
            'Одежда и другие товары лёгкой промышленности',
        ];

        foreach ($markingTypes as $name) {
            MarkingType::firstOrCreate(
                ['name' => $name],
                ['honest' => true],
            );
        }
    }
}
