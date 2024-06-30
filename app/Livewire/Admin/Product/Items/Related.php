<?php

namespace App\Livewire\Admin\Product\Items;

use App\Modules\Product\Entity\Product;
use Livewire\Attributes\On;
use Livewire\Component;

class Related extends Component
{

    public Product $product;

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->refresh_fields();
    }

    #[On('update-related')]
    public function refresh_fields()
    {
        $this->product->refresh();
    }

    #[On('add-related')]
    public function add($product_id)
    {
        if ($this->product->id === $product_id) throw new \DomainException('Товар совпадает с текущим');
        if ($this->product->isRelated($product_id)) throw new \DomainException('Товар уже добавлен в Аксессуары');
        $this->product->related()->attach($product_id);
        $this->product->refresh();
        $this->dispatch('clear-search-product');
        $this->dispatch('update-related');
    }

    public function render()
    {
        return view('livewire.admin.product.items.related');
    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка при добавлении Аксессуара', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }
}
