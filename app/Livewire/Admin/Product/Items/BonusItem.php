<?php

namespace App\Livewire\Admin\Product\Items;

use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Bonus as ProductBonus;
use Livewire\Component;

class BonusItem extends Component
{

    public Product $product;
    public float $discount;
    public Product $bonus;

    public function mount(Product $bonus, Product $product)
    {
        $this->bonus = $bonus;
        $this->product = $product;
        $this->refresh_field();
    }

    public function refresh_field()
    {
        $this->discount = $this->bonus->pivot->discount;
    }

    public function remove()
    {
        $this->product->bonus()->detach($this->bonus->id);
        $this->dispatch('update-bonus');
    }

    public function save()
    {
        $this->product->bonus()->updateExistingPivot($this->bonus->id, ['discount' => $this->discount]);
    }

    public function render()
    {
        return view('livewire.admin.product.items.bonus-item');
    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка с ценой бонуса', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }
}
