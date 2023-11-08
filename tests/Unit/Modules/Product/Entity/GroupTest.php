<?php
declare(strict_types=1);

namespace Modules\Product\Entity;

use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreate(): void
    {
        $category = Category::register($category_name = 'Категория');
        $product1 = Product::register($product1_name = 'Товар 1', 'code 1', $category->id);
        $product2 = Product::register($product2_name = 'Товар 2', 'code 2', $category->id);
        $group = Group::register($group_name = 'Группа 1');
        $group->products()->attach($product1->id);
        $group->products()->attach($product2->id);
        foreach ($product1->groups as $_group) {
            self::assertEquals($_group->name, $group_name);
            self::assertCount( 2, $_group->products);

        }
        self::assertCount( 2, $group->products);
    }
}
