<?php

namespace App\Livewire\Cabinet\Cart;

use App\Modules\User\Entity\User;
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
    public bool $button_trash;
    public bool $check_all;
    public mixed $user;

    public function boot()
    {
        $this->cart = app()->make('\App\Modules\Shop\Cart\Cart');
    }

    public function mount(mixed $user)
    {
        $this->user = $user;
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
        $this->button_trash = false;
        foreach ($this->items as $item) {
            if ($item['check']) $this->button_trash = true;
        }

    }

    public function check_items()
    {
        $this->cart->check_all($this->check_all);
        $this->dispatch('update-header-cart');
        $this->dispatch('update-item-cart');
    }

    public function del_select()
    {
        $this->cart->clear_check();
        $this->dispatch('update-header-cart');
    }

    public function render()
    {
        return view('livewire.cabinet.cart.cart-page');
    }
}
