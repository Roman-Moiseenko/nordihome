<?php

namespace App\Livewire\Admin\Product\Items;

use App\Modules\Product\Entity\Product;
use App\Modules\Product\Service\ProductService;
use Livewire\Attributes\On;
use Livewire\Component;

class Bonus extends Component
{

    public Product $product;

   /* public ProductService $service;

    public function boot(ProductService $service)
    {
        $this->service = $service;
    }
*/
    public function mount(Product $product)
    {
        $this->product = $product;
        $this->refresh_fields();
    }

    #[On('update-bonus')]
    public function refresh_fields()
    {
        $this->product->refresh();
    }

    #[On('add-bonus')]
    public function add($product_id)
    {

        $bonus = \App\Modules\Product\Entity\Bonus::where('bonus_id', $product_id)->first();
        if (!is_null($bonus)) throw new \DomainException('Товар уже назначен бонусным у товара ' . $bonus->product->name);

        $bonus_add = Product::find($product_id);
        $this->product->bonus()->attach($product_id, ['discount' => $bonus_add->getLastPrice()]);

        $this->refresh_fields();
        $this->dispatch('clear-search-product');
        $this->dispatch('update-bonus');
    }


    public function render()
    {
        return view('livewire.admin.product.items.bonus');
    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка в Уведомлениях', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }
}
