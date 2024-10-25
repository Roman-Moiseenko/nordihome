<?php

namespace App\Livewire\Admin\Product\Items;

use App\Modules\Accounting\Service\BalanceProductService;
use App\Modules\Admin\Entity\Options;
use App\Modules\Product\Entity\Product;
use App\Modules\Setting\Repository\SettingRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
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
    public bool $not_sale;

    public bool $shop_pre_order;
    public bool $only_offline;
    public bool $accounting;

    public int $balance_min;
    public ?int $balance_max;
    public bool $balance_buy;

    public function boot()
    {

        $settings = new SettingRepository();
        $common = $settings->getCommon();
        $this->accounting = $common->accounting;

        $this->shop_pre_order = $common->pre_order;
        $this->only_offline = $common->only_offline;
    }

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->refresh_fields();
    }

    public function refresh_fields(): void
    {
        $this->published = $this->product->published;
        $this->price = $this->product->getPriceRetail();
        $this->count = $this->product->getCountSell();

        $this->pre_order = $this->shop_pre_order && $this->product->pre_order;
        $this->offline = $this->only_offline || $this->product->only_offline;
        $this->not_sale = $this->product->not_sale;

        $this->priority = $this->product->priority;

        $this->frequency = $this->product->frequency;

        $this->balance_min = $this->product->balance->min;
        $this->balance_max = $this->product->balance->max;
        $this->balance_buy = $this->product->balance->buy ?? true;

    }


    /**
     * @throws BindingResolutionException
     */
    public function save(): void
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
        $this->product->not_sale = $this->not_sale;
        $this->product->save();

        //dd([!$this->product->isPublished() , $this->published]);
        if (!$this->product->isPublished() && $this->published) {

            $service = app()->make('\App\Modules\Product\Service\ProductService');
            $service->published($this->product);
        } elseif ($this->product->isPublished() && !$this->published) {
            $this->product->published = false;
            $this->product->save();
        }

        $serviceBalance = app()->make(BalanceProductService::class);
        $serviceBalance->setBalance(
            $this->product,
            $this->balance_min,
            empty($this->balance_max) ? null : $this->balance_max,
            $this->balance_buy
        );
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
