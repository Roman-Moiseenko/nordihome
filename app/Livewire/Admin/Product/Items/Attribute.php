<?php

namespace App\Livewire\Admin\Product\Items;

use App\Modules\Product\Entity\Product;
use Livewire\Attributes\On;
use Livewire\Component;

class Attribute extends Component
{
    public Product $product;
    public array $prod_attributes;
    public ?int $attribute_id;

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->refresh_fields();
    }

    #[On('update-attribute')]
    public function refresh_fields()
    {
        $this->product->refresh();
        $this->prod_attributes = $this->product->getPossibleAttribute();
    }

    public function add()
    {
        if (!is_null($this->attribute_id) && $this->attribute_id != 0) {
            /** @var \App\Modules\Product\Entity\Attribute $attribute */
            $attribute = \App\Modules\Product\Entity\Attribute::find($this->attribute_id);
            $value = [];
            if ($attribute->isBool()) $value = true;
            if ($attribute->isNumeric()) $value = 0;

            $this->product->prod_attributes()->attach($this->attribute_id, ['value' => json_encode($value)]);
        }
    }

    #[On('change-category')]
    public function check_category()
    {
        $this->product->refresh();
        $this->prod_attributes = $this->product->getPossibleAttribute();

        $array = array_map(function (\App\Modules\Product\Entity\Attribute $attribute) {
            return $attribute->id;
        }, $this->prod_attributes);

        foreach ($this->product->prod_attributes as $attribute) {
            if (!in_array($attribute->id, $array)) {
                $this->product->prod_attributes()->detach($attribute->id);
            }
        }
        $this->refresh_fields();
    }

    public function render()
    {
        return view('livewire.admin.product.items.attribute');
    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }
}
