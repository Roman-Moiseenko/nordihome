<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart;

class CartInfo
{
    public CartInfoBlock $all;
    public CartInfoBlock $order;
    public CartInfoBlock $pre_order;
    public bool $check_all;
    public bool $preorder;

    public function __construct()
    {
        $this->all = new CartInfoBlock();
        $this->order = new CartInfoBlock();
        $this->pre_order = new CartInfoBlock();
        $this->preorder = false;
        $this->check_all = true;
    }

    public function clear()
    {
        $this->all->clear();
        $this->order->clear();
        $this->pre_order->clear();
        $this->check_all = true;
        $this->preorder = false;
    }
}
