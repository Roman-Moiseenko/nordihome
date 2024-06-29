<?php

namespace App\Livewire\Admin\Product;

use App\Modules\Product\Entity\Product;
use Livewire\Component;

class Dimensions extends Component
{

    public Product $product;

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function render()
    {
        return view('livewire.admin.product.dimensions');
    }
}
