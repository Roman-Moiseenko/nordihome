<?php
declare(strict_types=1);

namespace Modules\Product\Entity;

use App\Entity\Dimensions;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Photo;
use App\Modules\Product\Entity\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreate(): void
    {
        //Имя и Слаг
        $product = Product::register($name = 'name', $code = '7889-GH-987-Y');

        self::assertEquals($product->getSlug(), $name);
        $product->setSlug($slug = 'name-product');
        self::assertEquals($product->getSlug(), $slug);
        self::assertEquals($product->getName(), $name);

        //Публикация
        $product->published();
        $this->expectExceptionMessage('Нельзя опубликовать незаполненный товар.');
        self::assertFalse($product->isVisible());

        $product->setDescription('Описание');
        //self::assertEquals($product->getCode(), $code);

        //Бренд
        $brand = Brand::register('Ikea', 'description', 'url', ['sameAs1', 'sameAs2', 'sameAs3']);
        $product->setBrand($brand);
        self::assertEquals($product->brand->url, 'url');



        $photo = Photo::new('file', 'path');
        $product->setMainPhoto($photo);
        self::assertEquals($product->getPhotoUrl(), 'path/file' );


        $product->published();
        //Тип продажи
        $product->setType(Product::ONLINE);
        $product->setCount(100);
        self::assertTrue($product->isVisible());
        $product->sell(100);
        self::assertFalse($product->isVisible()); //?
        $product->setType(Product::ORDER);
        self::assertTrue($product->isVisible());

        $product->setType(Product::OFFLINE);
        self::assertFalse($product->isVisible());
        $product->setCount(100);
        self::assertFalse($product->isVisible());


        //Габариты
        $dimensions = Dimensions::create($width = 45, $height = 100, $depth = 20, $weight = 12.5); //Для Shop
        //$package = Dimensions::create($width2 = 20, $height2 = 40, $depth2 = 15, $weight2 = 14); //Для Delivery
        $product->setDimensions($dimensions);
        //$product->setPackage($package);
        $product->getWidth();
        self::assertEquals($product->getWidth(), $width );

    }

    public function testPricing(): void
    {
        $product = Product::create('name', '7889-GH-987-Y');
        $product->setPrice($price = 80);
        self::assertEquals($product->currentPrice(), $price);

        $product->setPrice($price2 = 100, $doc = 'Проводка 7 от 04/08/2023');
        self::assertEquals($product->currentPrice(), $price2);
        self::assertEquals($product->oldPrice(), $price);

        $array = $product->getPricing();
        self::assertIsArray($array);

        $pricing1 = $array[0];
        $pricing2 = $array[1];
        self::assertEquals($pricing1->value, $price);
        self::assertEquals($pricing2->value, $price2);

        self::assertEmpty($pricing1->fixid_doc);
        self::assertEquals($pricing2->fixid_doc, $doc);
    }

    public function testDublicate(): void
    {
        $name = 'Товар новый';
        Product::create($name, 'code1');
        Product::create($name, 'code2');
        $this->expectExceptionMessage('Дублирование. Товар ' . $name . ' уже существует');

        $code = '7889-GH-987-Y';
        Product::create('name1', $code);
        Product::create('name2', $code);
        $this->expectExceptionMessage('Дублирование. Товар с артикулом ' . $code . ' уже существует');
    }


    public function testPhoto(): void
    {

    }

    public function testVideo(): void
    {

    }
}
