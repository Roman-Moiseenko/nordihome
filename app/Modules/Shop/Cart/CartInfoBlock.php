<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart;

class CartInfoBlock
{
    public int $count = 0;
    public float $amount = 0;
    public float $discount = 0;
/*
    public function __construct()
    {
        $this->amount = 0;
        $this->count = 0;
        $this->discount = 0;
    }
*/
    public function clear()
    {
        $this->amount = 0;
        $this->count = 0;
        $this->discount = 0;
    }
}
