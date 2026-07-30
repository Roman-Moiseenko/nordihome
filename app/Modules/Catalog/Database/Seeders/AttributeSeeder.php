<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Database\Seeders;

use App\Modules\Catalog\Entity\Attribute;
use App\Modules\Catalog\Entity\AttributeGroup;
use App\Modules\Catalog\Infrastructure\Models\Category;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Группа атрибутов по умолчанию
        $group = AttributeGroup::firstOrCreate(
            ['name' => 'Основные характеристики'],
            ['sort' => 1]
        );

        // Атрибуты для создания
        $attributes = [
            'Цвет',
            'Материал',
        ];

        // ID корневых категорий (без родителя)
        $rootCategoryIds = Category::whereIsRoot()->pluck('id')->toArray();

        foreach ($attributes as $name) {
            /** @var Attribute $attribute */
            $attribute = Attribute::firstOrCreate(
                [
                    'name' => $name,
                    'type' => Attribute::TYPE_VARIANT,
                ],
                [
                    'group_id' => $group->id,
                    'multiple' => true,
                    'filter' => true,
                    'show_in' => true,
                ]
            );

            // Синхронизация с корневыми категориями
            if (!empty($rootCategoryIds)) {
                $attribute->categories()->syncWithoutDetaching($rootCategoryIds);
            }
        }
    }
}
