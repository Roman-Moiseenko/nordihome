<?php

namespace App\Livewire\Admin\Product\Items;

use App\Modules\Product\Entity\Product;
use Livewire\Attributes\On;
use Livewire\Component;

class Option extends Component
{
    public Product $product;

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->refresh_fields();
    }

    #[On('update-option')]
    public function refresh_fields()
    {

    }

    public function render()
    {
        return view('livewire.admin.product.items.option');
    }
}
