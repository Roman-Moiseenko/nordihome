<?php

namespace App\Livewire\Admin\Product\Items;

use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Equivalent as ProductEquivalent;
use App\Modules\Product\Service\EquivalentService;
use Livewire\Component;

class Equivalent extends Component
{

    public Product $product;
    public ProductEquivalent $equivalents;
    private EquivalentService $service;

    public function boot(EquivalentService $service)
    {
        $this->service = $service;
    }

    public function mouth(Product $product)
    {
        $this->product = $product;
        $this->equivalents = ProductEquivalent::orderBy('name')->where('category_id', '=', $product->main_category_id)->get();
    }

    public function render()
    {
        return view('livewire.admin.product.items.equivalent');
    }
}
