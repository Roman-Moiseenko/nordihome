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



    public function testChildCategory(): void
    {
        $parent = Category::register('parent category');
        $child1 = Category::register('child category', $parent->id);
        $child2 = Category::register('child category', $parent->id);

        self::assertTrue($child1->equilParent($child2));
        self::assertTrue($child1->isParent($parent));
        self::assertFalse($parent->isParent($parent));
    }




}
