<?php
declare(strict_types=1);

namespace Modules\Product\Entity;

use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\AttributeGroup;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Modification;
use App\Modules\Product\Entity\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ModificationTest extends TestCase
{
    use DatabaseTransactions;
    public function testCreate(): void
    {
        $category = Category::register($category_name = 'Категория');
        $product1 = Product::register($product1_name = 'Товар 1', 'code 1', $category->id);
        $product2 = Product::register($product2_name = 'Товар 2', 'code 2', $category->id);
        $attribute_group = AttributeGroup::register('группа атрибутов');
        $attribute1 = Attribute::register('attribute 1', $attribute_group->id, Attribute::TYPE_VARIANT);
        $var11 = $attribute1->addVariant('Вариант 1.1');
        $var12 = $attribute1->addVariant('Вариант 1.2');
        $var13 = $attribute1->addVariant('Вариант 1.3');
        $attribute2 = Attribute::register('attribute 2', $attribute_group->id, Attribute::TYPE_VARIANT);
        $var21 = $attribute2->addVariant('Вариант 2.1');
        $var22 = $attribute2->addVariant('Вариант 2.2');
        //$attribute2 = Attribute::register('attribute 2', $attribute_group->id, Attribute::TYPE_INTEGER);
        //$modification2 = Modification::register('Товар 1', $product1->id, [$attribute2->id]);
        ///Исключение
        ///
        $modification = Modification::register($mod_name = 'Товар 1', $product1->id, [$attribute1, $attribute2]);
        $array2 = $modification->getVariations();
        self::assertCount(6, $array2);
        $array1 = [
            [
                $attribute1->id => $var11->id,
                $attribute2->id => $var21->id,
            ],
            [
                $attribute1->id => $var11->id,
                $attribute2->id => $var22->id,
            ],
            [
                $attribute1->id => $var12->id,
                $attribute2->id => $var21->id,
            ],
            [
                $attribute1->id => $var12->id,
                $attribute2->id => $var22->id,
            ],
            [
                $attribute1->id => $var13->id,
                $attribute2->id => $var21->id,
            ],
            [
                $attribute1->id => $var13->id,
                $attribute2->id => $var22->id,
            ],

        ];
        self::assertEquals($array1, $array2);

        $modification->products()->attach($product1->id, ['values_json' => json_encode([$attribute1->id => $var11->id, $attribute2->id => $var21->id,])]);
        $modification->products()->attach($product2->id, ['values_json' => json_encode([$attribute1->id => $var11->id, $attribute2->id => $var22->id,])]);
        self::assertEquals($product1->modification->name,$mod_name);
    }

}
