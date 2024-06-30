<?php

namespace App\Livewire\Admin\Product\Items;

use App\Modules\Product\Entity\Product;
use Livewire\Component;

class RelatedItem extends Component
{

    public Product $related;
    public Product $product;

    public function mount(Product $related, Product $product)
    {
        $this->related = $related;
        $this->product = $product;
        //$this->refresh_field();
    }

    public function remove()
    {
        $this->product->related()->detach($this->related->id);
        $this->dispatch('update-related');
    }

    public function render()
    {
        return view('livewire.admin.product.items.related-item');
    }
}
