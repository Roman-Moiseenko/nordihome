<?php

namespace App\Livewire\Cabinet\Cart;

use Livewire\Component;

class CartInfo extends Component
{

    public int $count;
    public float $amount;
    public float $discount;

    public function render()
    {
        return view('livewire.cabinet.cart.cart-info');
    }
}
