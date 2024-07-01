<?php

namespace App\Livewire\Admin\Product\Items;

use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Equivalent as ProductEquivalent;
use App\Modules\Product\Service\EquivalentService;
use Livewire\Attributes\On;
use Livewire\Component;
use Tests\CreatesApplication;

class Equivalent extends Component
{



    public Product $product;
    public mixed $equivalents;
    private EquivalentService $service;
    public int $equivalent_id;

    public function boot()
    {
       $this->service = new EquivalentService();
    }

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->refresh_fields();
    }

    #[On('update-equivalent')]
    public function refresh_fields()
    {
        $this->equivalent_id = is_null($this->product->equivalent_product) ? 0 : $this->product->equivalent_product->equivalent_id;
        $product = $this->product;
        $this->equivalents = ProductEquivalent::orderBy('name')
            ->whereHas('category', function ($query) use ($product) {
                $query->where('_lft', '<=' ,$product->category->_lft)
                    ->where('_rgt', '>=' ,$product->category->_rgt);
            })
            ->get();
    }

    public function change()
    {
        if ($this->equivalent_id == 0 && !is_null($this->product->equivalent_product)) {
            $this->service->delProductByIds($this->product->equivalent_product->equivalent_id, $this->product->id);
        }
        if ($this->equivalent_id != 0) {
            if (is_null($this->product->equivalent_product)) {
                //Доб.новый
                $this->service->addProductByIds($this->equivalent_id, $this->product->id);
            } elseif ($this->equivalent_id !== $this->product->equivalent_product->equivalent_id) {
                $this->service->delProductByIds($this->product->equivalent_product->equivalent_id, $this->product->id);
                $this->service->addProductByIds($this->equivalent_id, $this->product->id);
            }
        }

        $this->refresh_fields();
        $this->dispatch('update-equivalent');
    }

    public function render()
    {
        return view('livewire.admin.product.items.equivalent');
    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }
}
