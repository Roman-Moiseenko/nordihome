<?php
declare(strict_types=1);

namespace Modules\Product\Entity;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AttributeTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreate(): void
    {
        $attribute = Attribute::creat($name = 'name attribute');
        $attribute->addValue($value1 = 'value 1');
        $attribute->addValue($value2 = 'value 2');

        //Настройка параметров
        $values = $attribute->values();
        self::assertIsArray($values);

        self::assertEquals($values[0]->value, $value1);
        self::assertEquals($values[1]->value, $value2);

    }

    public function testAssignProduct(): void
    {
        $category1 = Category::create('category 1');
        $category2 = Category::create('category 2');

        $attribute = Attribute::creat($name = 'name attribute', $category1);
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
        self::assertObjectEquals($array[0]->value, $value1);
    }
}
