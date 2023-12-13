<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart;

class CartInfoBlock
{
    public int $count;
    public float $amount;
    public float $discount;

    public function clear()
    {
        $this->amount = 0;
        $this->count = 0;
        $this->discount = 0;
    }
}
