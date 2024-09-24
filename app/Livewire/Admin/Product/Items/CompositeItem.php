<?php

namespace App\Livewire\Admin\Product\Items;

use App\Modules\Product\Entity\Product;
use Livewire\Component;

class CompositeItem extends Component
{
    public Product $product;
    public Product $composite;
    public int $quantity;

    public function mount(Product $composite, Product $product)
    {
        $this->composite = $composite;
        $this->product = $product;
        $this->refresh_fields();
    }

    public function render()
    {
        return view('livewire.admin.product.items.composite-item');
    }

    private function refresh_fields()
    {
        $this->quantity = $this->composite->pivot->quantity;
    }

    public function remove()
    {
        $this->product->composites()->detach($this->composite->id);
        $this->dispatch('update-composite');
    }

    public function save()
    {
        $this->product->composites()->updateExistingPivot($this->composite->id, ['quantity' => $this->quantity]);
    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка с составным товаром', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }
}
