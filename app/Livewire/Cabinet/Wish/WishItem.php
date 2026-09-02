<?php

namespace App\Livewire\Cabinet\Wish;

use App\Modules\Cabinet\Application\Actions\Wish\RemoveWishUseCase;
use App\Modules\Cabinet\Infrastructure\Models\Wish;
use App\Modules\Catalog\Infrastructure\Models\Product;
use Livewire\Component;

class WishItem extends Component
{
    public array $wish;
    private RemoveWishUseCase $removeWishUseCase;

    public function boot(
        RemoveWishUseCase $removeWishUseCase,
    ): void
    {
        $this->removeWishUseCase = $removeWishUseCase;
    }

    public function mount(mixed $wish)
    {
        $this->wish = $wish;
    }

    public function remove(): void
    {
        $productId = $this->removeWishUseCase->execute($this->wish['id']);

        $this->dispatch('update-wish', product_id: $productId);
        $this->dispatch('update-header-wish');
    }

    public function render()
    {
        return view('livewire.cabinet.wish.wish-item');
    }
}
