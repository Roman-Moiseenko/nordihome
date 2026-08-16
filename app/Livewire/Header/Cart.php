<?php

namespace App\Livewire\Header;

use App\Modules\Cart\Application\Actions\ClearCartUseCase;
use App\Modules\Cart\Application\Actions\GetCartUseCase;
use App\Modules\Cart\Application\Actions\RemoveCartItemUseCase;
use App\Modules\Cart\Application\DTOs\CartItemData;
use App\Modules\Cart\Domain\Entities\Cart as CartEntity;
use Livewire\Attributes\On;
use Livewire\Component;

class Cart extends Component
{
  //  private CartEntity $cart;
    public string $test = '';
    public int $count;
    public float $amount;
    public float $discount;
    //private mixed $tz;

    /** @var CartItemData[] $items  */
    public array $items;

    public function boot(): void
    {
    //    $this->cart = app()->make('\App\Modules\Cart\Domain\Entities\Cart');
    }


    public function mount(): void
    {
     //   $this->cart = app()->make('\App\Modules\Cart\Domain\Entities\Cart');
        $this->refresh_fields();
    }


    #[On('update-header-cart')]
    public function refresh_fields(): void
    {
        $useCase = app()->make(GetCartUseCase::class);
        $data = $useCase->execute();
        $this->items = json_decode(json_encode($data->items), true);
        $this->amount = $data->amount; // $this->cart->info->all->amount;
        $this->discount = $data->discount; //$this->cart->info->all->discount;
        $this->count = $data->quantity; //$this->cart->info->all->count;
    }

    public function del_item($id, RemoveCartItemUseCase $useCase): void
    {
        $quantity = $useCase->execute($id);

        $this->dispatch('e-cart', product_id: $id, e_type: 'remove', quantity: $quantity);
        $this->dispatch('update-header-cart');
    }

    public function clear_cart(GetCartUseCase $cartUseCase, ClearCartUseCase $clearUseCase): void
    {
        $items = $cartUseCase->execute()->items;
        foreach ($items as $item) {
            $this->dispatch('e-cart',
                product_id: $item->productId, e_type: 'remove', quantity: $item->quantity);
        }
        $clearUseCase->execute();

        $this->refresh_fields();
        $this->dispatch('update-header-cart');
    }

    public function render()
    {
        return view('livewire.header.cart');
    }
}
