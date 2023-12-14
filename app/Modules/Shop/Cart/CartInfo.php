<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart;

class CartInfo
{
    public CartInfoBlock $all;
    public CartInfoBlock $order;
    //public CartInfoBlock $preorder;
    public bool $check_all;
    public bool $preorder;

    public function __construct()
    {
        $this->all = new CartInfoBlock();
        $this->order = new CartInfoBlock();
        //$this->preorder = new CartInfoBlock();
        $this->preorder = false;
        $this->check_all = true;
    }

    public function clear()
    {
        $this->all->clear();
        $this->order->clear();
        //$this->preorder->clear();
        $this->check_all = true;
        $this->preorder = false;
    }
}
