<?php

namespace App\Livewire\Admin\Product\Items;

use App\Modules\Product\Entity\Product;
use Livewire\Component;

class AttributeItem extends Component
{

    public Product $product;
    public float $discount;
    public \App\Modules\Product\Entity\Attribute $attribute;
    /**
     * @var mixed|null
     */
    public mixed $value;
    public mixed $_value;
    public mixed $_key;

    public function mount(\App\Modules\Product\Entity\Attribute $attribute, Product $product)
    {
        $this->attribute = $attribute;
        $this->product = $product;
        $this->value = $product->Value($attribute->id);
        $this->refresh_fields();
        $this->_key = $attribute->id . '-' . rand(1000,9999);
    }

    public function refresh_fields()
    {
        $this->_value = $this->product->Value($this->attribute->id);
    }

    public function save()
    {
        //Атрибут участвует в модификации
        if ($this->product->AttributeIsModification($this->attribute->id)) {
            $old = $this->product->Value($this->attribute->id);
            if ((is_array($old) && !in_array($this->_value, $old))) {
                $this->refresh_fields();
                $this->dispatch(
                    'window-notify',
                    title: 'Ошибка',
                    message: 'Данный атрибут является ключевым в модификации товара и не подлежит изменению');
                $this->dispatch(
                    'tom-select-sync',
                    id: ('select-variant-' . (string)$this->attribute->id),
                    value: (int)array_shift($old));
            }
            return;
        }

        if ($this->attribute->isVariant() && !is_array($this->_value)) $this->_value = [$this->_value];
        $this->product->prod_attributes()->updateExistingPivot($this->attribute->id, ['value' => json_encode($this->_value)]);
    }

    public function remove()
    {
        $this->product->prod_attributes()->detach($this->attribute->id);
        $this->dispatch('update-attribute');
    }

    public function render()
    {
        return view('livewire.admin.product.items.attribute-item');
    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }
}
