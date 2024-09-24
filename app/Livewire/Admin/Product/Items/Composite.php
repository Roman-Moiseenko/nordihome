<?php

namespace App\Livewire\Admin\Product\Items;

use App\Modules\Product\Entity\Product;
use Livewire\Attributes\On;
use Livewire\Component;

class Composite extends Component
{

    public Product $product;

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->refresh_fields();
    }

    #[On('update-composite')]
    public function refresh_fields()
    {
        $this->product->refresh();
    }

    #[On('add-composite')]
    public function add($product_id, $quantity = 1)
    {
        if ($this->product->id === $product_id) throw new \DomainException('Товар совпадает с текущим');
        if ($this->product->isComposite($product_id)) throw new \DomainException('Товар уже добавлен в Составной');

        if (!is_null(\App\Modules\Product\Entity\Composite::where('child_id', $this->product->id)->first()))
            throw new \DomainException('Текущий товар уже является составным');


        $this->product->composites()->attach($product_id, ['quantity' => $quantity]);

        $this->refresh_fields();
        $this->dispatch('clear-search-product');
        $this->dispatch('update-composite');
    }


    public function render()
    {
        return view('livewire.admin.product.items.composite');
    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка с составным товаром', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }

}
