<?php

namespace App\Livewire\Admin\Product\Items;

use App\Modules\Admin\Entity\Options;
use App\Modules\Product\Entity\Product;
use Livewire\Component;

class Management extends Component
{

    public Product $product;

    public bool $published;
    public float $price;
    public int $count;
    public bool $pre_order;
    public bool $offline;
    public int $frequency;
    public bool $priority;

    public bool $shop_pre_order;
    public bool $only_offline;
    public bool $accounting;

    public function boot()
    {
        $options = new Options();
        $this->accounting = $options->shop->accounting;
        $this->shop_pre_order = $options->shop->pre_order;
        $this->only_offline = $options->shop->only_offline;
    }

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->refresh_fields();
    }

    public function refresh_fields()
    {
        $this->published = $this->product->published;
        $this->price = $this->product->getLastPrice();
        $this->count = $this->product->getCountSell();

        $this->pre_order = $this->shop_pre_order && $this->product->pre_order;
        $this->offline = $this->only_offline || $this->product->only_offline;

        $this->priority = $this->product->priority;

        $this->frequency = $this->product->frequency;
    }


    public function save()
    {
        //$this->product->published = $this->published;
        if (!$this->accounting) {
            $this->product->setPrice($this->price);
            $this->product->setCountSell($this->count);
        }

        if ($this->shop_pre_order) $this->product->pre_order = $this->pre_order;
        if (!$this->only_offline) $this->product->only_offline = $this->offline;

        $this->product->frequency = $this->frequency;
        $this->product->priority = $this->priority;
        $this->product->save();

        if (!$this->product->isPublished() && $this->published) {
            $service = app()->make('\App\Modules\Product\Service\ProductService');
            $service->published($this->product);
        }

    }

    public function render()
    {
        return view('livewire.admin.product.items.management');
    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }
}
