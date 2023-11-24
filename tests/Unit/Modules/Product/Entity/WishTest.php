<?php
declare(strict_types=1);

namespace Modules\Product\Entity;

use App\Entity\User\User;
use App\Modules\Product\Entity\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WishTest extends TestCase
{
    use DatabaseTransactions;


    public function testWish(): void
    {
      /*  $product = Product::create('name');
        $user = User::new('email', 'phone');
        $user2 = User::new('email2', 'phone2');
        $user->addWish($product);
        self::assertEquals($product->wishCount(), 1);
        $user2->addWish($product);
        self::assertEquals($product->wishCount(), 2);*/
    }
}
