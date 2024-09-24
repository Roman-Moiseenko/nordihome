<?php

namespace App\Livewire\Admin\Product\Items;

use App\Modules\Base\Entity\Package;
use App\Modules\Product\Entity\Product;
use Livewire\Component;

class DimensionsItem extends Component
{

    public Package $package;
    public Product $product;

    public function mount(Package $package, Product $product)
    {
        $this->package = $package;
        $this->product = $product;
        $this->refresh_fields();
    }

    public function render()
    {
        return view('livewire.admin.product.items.dimensions-item');
    }

    public function refresh_fields()
    {
    }
}
