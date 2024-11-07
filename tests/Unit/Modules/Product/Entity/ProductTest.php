<?php
declare(strict_types=1);

namespace Modules\Product\Entity;

use App\Modules\Base\Entity\Dimensions;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Photo;
use App\Modules\Product\Entity\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreate(): void
    {
        //Основные параметры
        $category = Category::register('Category');
        $product = Product::register($name = 'name', $code = '7889-GH-987-Y', $category->id);

        self::assertEquals($product->getSlug(), $name);
        $product->setSlug($slug = 'name-product');
        self::assertEquals($product->getSlug(), $slug);
        self::assertEquals($product->getName(), $name);

        //Публикация
        //$product->published();
     /*   $this->expectExceptionMessage('Нельзя опубликовать незаполненный товар.');
        self::assertFalse($product->isVisible());*/

        $product->setDescription('Описание');
        //self::assertEquals($product->getCode(), $code);

        //Бренд
        $brand = Brand::register('Ikea', 'description', 'url', ['sameAs1', 'sameAs2', 'sameAs3']);
        $product->brand_id = $brand->id;

        self::assertEquals('url', $product->brand->url);





/*
        $product->published();
        //Тип продажи
        $product->setType(Product::SELL_ONLINE);
        $product->setCount(100);
        self::assertTrue($product->isVisible());
        $product->sell(100);
        self::assertFalse($product->isVisible()); //?
        $product->setType(Product::SELL_ORDER);
        self::assertTrue($product->isVisible());

        $product->setType(Product::SELL_OFFLINE);
        self::assertFalse($product->isVisible());
        $product->setCount(100);
        self::assertFalse($product->isVisible());*/


        //Габариты
        $dimensions = \App\Modules\Base\Entity\Dimensions::create($width = 45, $height = 100, $depth = 20, $weight = 12500, $measure = Dimensions::MEASURE_G); //Для Shop
        //$package = Dimensions::create($width2 = 20, $height2 = 40, $depth2 = 15, $weight2 = 14); //Для Delivery
        $product->dimensions = $dimensions;
        //$product->setPackage($package);
       // $product->getWidth();
        //self::assertEquals($product->getWidth(), $width);
        self::assertEquals($product->weight(), 12.5);

    }

    public function testPricing(): void
    {

    }

    public function testDublicate(): void
    {

            $category = Category::register('Category');
            $name = 'Товар новый';
            Product::register($name, 'code1', $category->id);
            Product::register($name, 'code2', $category->id);


            self::expectException(\DomainException::class);
            self::expectExceptionMessage('Дублирование. Товар ' . $name . ' уже существует');



      /*  $code = '7889-GH-987-Y';
        Product::register('name1', $code, $category->id);
        Product::register('name2', $code, $category->id);
        $this->expectExceptionMessage('Дублирование. Товар с артикулом ' . $code . ' уже существует');*/
    }


    public function testPhoto(): void
    {

    }

    public function testVideo(): void
    {

    }

    public function testCategory(): void
    {
        $category = Category::register($name_parent = 'parent category');

        self::assertEmpty($category->products());
        $product = Product::register($name = 'name', $code = '7889-GH-987-Y');
        $product->setMainCategory($category);
        self::assertEmpty($category->products());
        self::assertNotEmpty($category->products());
        self::assertNotEmpty($category->AllProducts());

        $second1 = Category::register($name_second1 = 'second category 1');
        $second2 = Category::register($name_second2 = 'second category 2');

        self::assertNotEmpty($second2->products());


        $main = $product->getMainCategory();
        self::assertEquals($category->name, $main->name);

        $categories = $product->getCategories();

        self::assertEquals($categories[0]->name, $name_second1);
        self::assertEquals($categories[1]->name, $name_second2);
    }
}
