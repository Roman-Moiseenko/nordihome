<?php

namespace App\Livewire\Cabinet\Wish;

use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\Wish;
use Livewire\Component;

class WishItem extends Component
{
    public Product $product;
    public Wish $wish;

    public function mount(mixed $wish)
    {
        $this->wish = $wish;
        $this->product = $wish->product;
    }

    public function remove()
    {
        $this->wish->delete();
        $this->dispatch('update-wish');
        $this->dispatch('update-header-wish');
    }

    public function render()
    {
        return view('livewire.cabinet.wish.wish-item');
    }
}
