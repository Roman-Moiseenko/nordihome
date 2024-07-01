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
    public int $equivalent_id;

    public function boot(EquivalentService $service)
    {
        $this->service = $service;
    }

    public function mouth(Product $product)
    {
        $this->product = $product;
        $this->equivalent_id = ($product->equivalent_product) ? 0 : $product->equivalent_product->equivalent_id;
        $this->equivalents = ProductEquivalent::orderBy('name')
            ->whereHas('category', function ($query) use ($product) {
                $query->where('_lft', '<=' ,$product->category->_lft)
                    ->where('_rgt', '>=' ,$product->category->_rgt);
            })
            ->get();
    }

    public function change()
    {

    }

    public function render()
    {
        return view('livewire.admin.product.items.equivalent');
    }
}
