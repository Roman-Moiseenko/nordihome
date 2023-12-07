<?php
declare(strict_types=1);

namespace Modules\User\Entity;

use App\Modules\Order\Entity\Reserve;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReserveTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreate()
    {
        $user = User::new('email', 'phone');
        $category = Category::register('category');
        $product = Product::register('tovar', 'code', $category->id);
        $reserve = Reserve::register($product->id, $count = 10, $user->id, 2, 'cart');
        self::assertEquals($count, $reserve->quantity);

        $add_count = 2;
        $reserve->updateReserve($add_count, 2);
        self::assertEquals($count + $add_count, $reserve->quantity);
    }

}
