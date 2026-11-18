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
         /*   ['name' => 'Способ размещения', 'name_en' => 'ListingFee', 'variants' => ['Package', 'BBL', 'PackageBBL']],
            ['name' => 'Способ связи', 'name_en' => 'ContactMethod', 'variants' => ['По телефону и в сообщениях', 'По телефону', 'В сообщениях']],
            ['name' => 'Адрес', 'name_en' => 'Address', 'variants' => []],
            ['name' => 'Номер телефона', 'name_en' => 'ContactPhone', 'variants' => []],
            ['name' => 'Почта', 'name_en' => null, 'variants' => []],
            ['name' => 'Вид продажи', 'name_en' => 'AdType', 'variants' => ['Товар куплен на продажу', 'Товар произведён мной']],
            ['name' => 'Состояние', 'name_en' => 'Condition', 'variants' => ['Новое', 'Б/у']],
            ['name' => 'Включая НДС', 'name_en' => 'NDS', 'variants' => ['Без НДС', '0', '5', '7', '10', '22']],*/
            ['name' => 'Описание после хар-к', 'name_en' => 'description_before', 'variants' => [], 'is_html' => true],
            ['name' => 'Описание до хар-к', 'name_en' => 'description_after', 'variants' => [], 'is_html' => true],
            ['name' => 'Описание для распродажи', 'name_en' => 'description_sale', 'variants' => [], 'is_html' => false],
            ['name' => 'Коэф. упаковки', 'name_en' => 'ratio_package', 'variants' => [], 'is_html' => false],
            ['name' => 'Авито %', 'name_en' => 'ratio_avito', 'variants' => [], 'is_html' => false],
        ];

        foreach ($parameters as $data) {
            $slug = $data['name_en'] !== null
                ? Str::slug($data['name_en'])
                : Str::slug($data['name']);

            AvitoParameter::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'name_en' => $data['name_en'],
                    'variants' => $data['variants'],
                    'is_html' => $data['is_html'] ?? false,
                ]
            );
        }
    }
}
