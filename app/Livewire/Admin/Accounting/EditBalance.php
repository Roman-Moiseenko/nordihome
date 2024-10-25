<?php

namespace App\Livewire\Admin\Accounting;

use App\Modules\Accounting\Service\BalanceProductService;
use App\Modules\Product\Entity\Product;
use Livewire\Component;

class EditBalance extends Component
{
    public Product $product;
    public int $min;
    public ?int $max;
    public bool $buy;

    public bool $change = false;

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->refresh_fields();
    }

    public function refresh_fields(): void
    {
        $this->min = $this->product->balance->min;
        $this->max = $this->product->balance->max;
        $this->buy = $this->product->balance->buy;
    }

    public function open_change(): void
    {
        $this->change = true;
    }

    public function save_change(): void
    {
        $serviceBalance = app()->make(BalanceProductService::class);
        $serviceBalance->setBalance(
            $this->product,
            $this->min,
            empty($this->max) ? null : $this->max,
            $this->buy
        );

        $this->change = false;
    }

    public function close_change(): void
    {
        $this->refresh_fields();
        $this->change = false;
    }
    public function render()
    {
        return view('livewire.admin.accounting.edit-balance');
    }
}
