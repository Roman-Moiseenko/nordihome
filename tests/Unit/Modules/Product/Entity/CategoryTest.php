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


/*
    public function testCreate(): void
    {
        $category = Category::register($name_parent = 'parent category');


        self::assertNull($category->parent());
        $child = $category->addChild($name_child = 'child category');

        self::assertEquals($child->name, $name_child);
        self::assertEquals($category->name, $child->parent()->name);
        self::assertEquals($category->name, $name_parent);
    }
*/
    public function testChildCategory(): void
    {
        $parent = Category::register('parent category');
        $child1 = Category::register('child category', $parent->id);
        $child2 = Category::register('child category', $parent->id);

        self::assertTrue($child1->equilParent($child2));
        self::assertTrue($child1->isParent($parent));
        self::assertFalse($parent->isParent($parent));


    /*    $product = Product::register($name = 'name', $code = '7889-GH-987-Y');
        $product->addCategory($parent);

        self::assertEmpty($parent->products());
        self::assertNotEmpty($parent->AllProducts());*/
    }




}
