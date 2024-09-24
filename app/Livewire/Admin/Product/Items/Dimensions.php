<?php

namespace App\Livewire\Admin\Product\Items;

use App\Modules\Admin\Entity\Options;
use App\Modules\Product\Entity\Product;
use App\Modules\Setting\Repository\SettingRepository;
use Livewire\Component;
use function view;

class Dimensions extends Component
{

    public Product $product;

    public float $weight;
    public float $height;
    public float $width;
    public float $depth;
    public string $measure;
    public int $type;

    public bool $local;
    public bool $delivery;

    public bool $delivery_local;
    public bool $delivery_all;


    public array $packages;

    public function boot()
    {
        $settings = new SettingRepository();
        $common = $settings->getCommon();
        $this->delivery_local = $common->delivery_local;
        $this->delivery_all = $common->delivery_all;
    }

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->refresh_fields();
    }

    public function refresh_fields()
    {

        $this->height = $this->product->dimensions->height;
        $this->width = $this->product->dimensions->width;
        $this->depth = $this->product->dimensions->depth;
        $this->type = $this->product->dimensions->type;

        $this->local = $this->delivery_local && $this->product->isLocal();
        $this->delivery = !$this->product->not_delivery;

        $this->packages = [];
        foreach ($this->product->packages->packages as $package)
            $this->packages[] = $package->toArray();
    }

    public function save()
    {

        $this->product->dimensions->height = $this->height;
        $this->product->dimensions->width = $this->width;
        $this->product->dimensions->depth = $this->depth;
        $this->product->dimensions->type = $this->type;

        $this->product->packages->cleare();

        foreach ($this->packages as $package) {
            $this->product->packages->create(
                params: $package
            );
        }

        if ($this->delivery_local) $this->product->not_local = !$this->local;
        if ($this->delivery_all) $this->product->not_delivery = !$this->delivery;

        $this->product->save();
    }

    public function add()
    {
        $this->product->packages->create();
        $this->product->save();
        $this->refresh_fields();
    }

    public function remove($key)
    {
        unset($this->packages[$key]);
        $this->save();
        $this->refresh_fields();
    }

    public function render()
    {
        return view('livewire.admin.product.items.dimensions');
    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }
}
