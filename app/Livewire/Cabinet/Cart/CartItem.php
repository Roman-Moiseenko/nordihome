<?php

namespace App\Livewire\Cabinet\Cart;

use Livewire\Attributes\On;
use Livewire\Component;

class CartItem extends Component
{

    /**
     * @var \App\Modules\Shop\Cart\Cart|mixed
     */
    private mixed $cart;
    public array $item;
    public int $quantity;

    public bool $check;

    public function boot()
    {
        $this->cart = app()->make('\App\Modules\Shop\Cart\Cart');
    }

    public function mount(array $item)
    {
        $this->item = $item;
        $this->quantity = $item['quantity'];
        $this->check = $item['check'];
    }

    #[On('update-item-cart')]
    public function refresh_data()
    {
        $this->cart->loadItems();
        $this->item = $this->cart->ItemData($this->cart->getItem($this->item['product_id']));
        $this->quantity = $this->item['quantity'];
        $this->check = $this->item['check'];
    }

    public function sub_item()
    {
        $this->quantity--;
        $this->cart->sub($this->item['product_id'], 1);
        $this->dispatch('update-header-cart');
        $this->dispatch('update-item-cart')->self();

    }

    public function plus_item()
    {
        $this->quantity++;
        $this->cart->plus($this->item['product_id'], 1);
        $this->dispatch('update-header-cart');
        $this->dispatch('update-item-cart')->self();

    }

    public function set_item()
    {
        $this->cart->set($this->item['product_id'], $this->quantity);
        $this->dispatch('update-header-cart');
        $this->dispatch('update-item-cart')->self();

    }

    public function check_item()
    {
        $this->cart->check($this->item['product_id']);
        $this->dispatch('update-header-cart');
    }

    public function render()
    {
        //$item = $this->item;
        return view('livewire.cabinet.cart.cart-item');
    }
}
