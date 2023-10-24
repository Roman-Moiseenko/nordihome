<?php
declare(strict_types=1);

namespace Modules\Product\Entity;

use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\AttributeGroup;
use App\Modules\Product\Entity\AttributeVariant;
use App\Modules\Product\Entity\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class AttributeTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreate(): void
    {
        $category = Category::register($name_category = 'Категория товара');
        $child = Category::register($name_child = 'Дочерняя категория товара');
        $category->children()->save($child);
        $group = AttributeGroup::register($name_group = 'Основные характеристики');
        $attribute = Attribute::register($name_attribute = 'name attribute', $group, $category, Attribute::TYPE_VARIANT);
        $attribute->multiple = true;
        $attribute->save();

        $attribute->addVariant(AttributeVariant::register($name_variant1 = 'Вариант 1'));
        $attribute->addVariant(AttributeVariant::register($name_variant2 = 'Вариант 2'));
        $attribute->push();

       foreach ($attribute->variants as $variant) {
           self::assertIsObject($variant, AttributeVariant::class);
           self::assertTrue(in_array($variant->name, [$name_variant1, $name_variant2]));
       }

        self::assertCount(2, $attribute->variants);
    }

    public function testAssignProduct(): void
    {
      /*  $category1 = Category::register('category 1');
        $category2 = Category::register('category 2');

        $attribute = Attribute::register($name = 'name attribute', $category1);
        $value1 = $attribute->addValue($value1 = 'value 1');

        $product = Product::create($name = 'name', $code = '7889-GH-987-Y');
        $product->assignValue($value1);
        $this->expectExceptionMessage('Атрибут не может быть назначен');
        $product->setMainCategory($category2);
        $product->assignValue($value1);
        $this->expectExceptionMessage('Атрибут не может быть назначен');
        $product->addCategory($category1);
        $product->assignValue($value1);

        $array = $product->Values();
        self::assertIsArray($array);
        self::assertObjectEquals($array[0]->value->attribute, $attribute);
        self::assertObjectEquals($array[0]->value, $value1);*/
    }
}
