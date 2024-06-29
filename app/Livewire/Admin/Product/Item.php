<?php

namespace App\Livewire\Admin\Product;

use App\Modules\Product\Entity\Product;
use Livewire\Component;

class Item extends Component
{

    public Product $product;
    public string $caption;
    public string $item;
    public string $element;

    public function mount(Product $product, string $item = '', string $caption = ' **** ')
    {
        $this->product = $product;
        $this->item = $item;
        $this->caption = $caption;
        $this->element = 'admin.product.items.' . $item;
    }

    public function render()
    {
        return view('livewire.admin.product.item');
    }
}
