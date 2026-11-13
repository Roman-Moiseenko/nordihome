<?php

namespace App\Modules\Marketplace\Database\Seeders;

use App\Modules\Marketplace\Infrastructure\Models\AvitoParameter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AvitoParameterSeeder extends Seeder
{
    public function run(): void
    {
        $parameters = [
            ['name' => 'Способ размещения', 'variants' => ['Package', 'BBL', 'PackageBBL']],
            ['name' => 'Способ связи', 'variants' => ['По телефону и в сообщениях', 'По телефону', 'В сообщениях']],
            ['name' => 'Адрес', 'variants' => []],
            ['name' => 'Номер телефона', 'variants' => []],
            ['name' => 'Вид продажи', 'variants' => ['Товар куплен на продажу', 'Товар произведён мной']],
            ['name' => 'Состояние', 'variants' => ['Новое', 'Б/у']],
            ['name' => 'Включая НДС', 'variants' => ['Без НДС', '0', '5', '7', '10', '22']],
            ['name' => 'description_before', 'variants' => [], 'is_html' => true],
            ['name' => 'description_after', 'variants' => [], 'is_html' => true],
        ];

        foreach ($parameters as $data) {
            AvitoParameter::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name' => $data['name'],
                    'variants' => $data['variants'],
                    'is_html' => $data['is_html'] ?? false,
                ]
            );
        }
    }
}
