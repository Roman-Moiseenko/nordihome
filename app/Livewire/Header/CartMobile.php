<?php

namespace App\Livewire\Header;

use App\Modules\Cart\Application\Actions\GetCartQuery;
use App\Modules\Shop\Application\Services\ClientContextFactory;
use Livewire\Attributes\On;
use Livewire\Component;

class CartMobile extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->refresh_fields();
    }
    #[On('update-header-cart')]
    public function refresh_fields(): void
    {

        $useCase = app()->make(GetCartQuery::class);
        $context = app(ClientContextFactory::class)->make();
        $data = $useCase->execute($context);


        $this->count = $data->quantity;
    }

    public function render()
    {
        return view('livewire.header.cart-mobile');
    }
}
