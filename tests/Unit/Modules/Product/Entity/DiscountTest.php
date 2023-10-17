<?php
declare(strict_types=1);

namespace Modules\Product\Entity;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiscountTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreate(): void
    {
        /*
        $discount = Discount::create();
        $product = Product::create();
        $discount->setProducts();
        $discount->start($enddate = time()+ 3600);

*/
    }
}
