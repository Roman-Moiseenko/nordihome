<?php

namespace App\Livewire\Cabinet\Cart;

use Livewire\Attributes\On;
use Livewire\Component;

use \App\Modules\Shop\Cart\Cart as CartEntity;

class CartPage extends Component
{

    private CartEntity $cart;

   // public Product $product;

    public array $items = [];
    public int $count;
    public float $amount;
    public float $discount;
    public bool $preorder;

    public bool $check_all;

    public function boot()
    {
        $this->cart = app()->make('\App\Modules\Shop\Cart\Cart');
    }

    public function  mount()
    {
        $this->refresh_data();
    }

    #[On('update-header-cart')]
    public function refresh_data()
    {
        $this->cart->loadItems();
        $this->items = $this->cart->ItemsData($this->cart->getItems());

        $this->amount = $this->cart->info->order->amount + $this->cart->info->pre_order->amount;
        $this->discount = $this->cart->info->order->discount + $this->cart->info->pre_order->discount;
        $this->count = $this->cart->info->order->count + $this->cart->info->pre_order->count;
        $this->preorder = $this->cart->info->preorder;
        $this->check_all = $this->cart->info->check_all;
    }

    public function check_items()
    {
        $this->cart->check_all($this->check_all);
        $this->refresh_data();
    }

    public function render()
    {
        //$items = $this->cart->getItems();
        return view('livewire.cabinet.cart.cart-page');
    }
}
