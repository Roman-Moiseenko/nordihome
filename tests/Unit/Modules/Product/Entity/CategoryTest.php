<?php
declare(strict_types=1);

namespace Modules\Product\Entity;

use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreate(): void
    {
        $category = Category::register($name_parent = 'parent category');


        self::assertNull($category->parent());
        $child = $category->addChild($name_child = 'child category');

        self::assertEquals($child->name, $name_child);
        self::assertEquals($category->name, $child->parent()->name);
        self::assertEquals($category->name, $name_parent);
    }

    public function testChildCategory(): void
    {
        $parent = Category::register($name_parent = 'parent category');
        $child = Category::register($name_child = 'child category', $parent);

        self::assertNotNull($child->parent());

    }

    public function testProductCategory(): void
    {
        $category = Category::register($name_parent = 'parent category');
        $product = Product::register($name = 'name', $code = '7889-GH-987-Y');
        $product->setMainCategory($category);

        $second1 = Category::register($name_second1 = 'second category 1');
        $second2 = Category::register($name_second2 = 'second category 2');

        $product->addCategory($category);
        $this->expectExceptionMessage('Категория уже назначен');

        $product->addCategory($second1);
        $product->addCategory($second2);

        $main = $product->getMainCategory();
        self::assertEquals($category->name, $main->name);

        $categories = $product->getCategories();

        self::assertEquals($categories[0]->name, $name_second1);
        self::assertEquals($categories[1]->name, $name_second2);

    }

}
