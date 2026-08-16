<?php

namespace App\Livewire\Cabinet\Cart;

use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Cart\Application\Actions\CheckToCartUseCase;
use App\Modules\Cart\Application\Actions\PlusToCartUseCase;
use App\Modules\Cart\Application\Actions\RemoveCartItemUseCase;
use App\Modules\Cart\Application\Actions\SetToCartUseCase;
use App\Modules\Cart\Application\Actions\SubToCartUseCase;
use App\Modules\User\Service\WishService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartItem extends Component
{

 //   private mixed $cart;
    public array $item;
    public int $quantity;
    private Client|null $client = null;

    public bool $wish;
    public bool $check;

    public function boot(): void
    {
     //   $this->cart = app()->make('\App\Modules\Cart\Domain\Entities\Cart');
    }

    public function mount(array $item, int|null $clientId): void
    {
        \Log::warning($clientId);
        $this->item = $item;
        $this->quantity = $item['quantity'];
        $this->check = $item['check'];
        $this->client = is_null($clientId) ? null : Client::find($clientId);
        $this->update_wish();
        $this->wish = !is_null($this->client) && ($this->client->isWish($this->item['productId']));
    }

    #[On('update-item-cart')]
    public function refresh_data(): void
    {
        /*
        $this->cart->loadItems();
        $this->item = $this->cart->ItemData($this->cart->getItem($this->item['productId']));
        $this->quantity = $this->item['quantity'];
        $this->check = $this->item['check'];
        */
    }

    #[On('update-wish')]
    public function update_wish($product_id = null): void
    {
        if (!is_null($product_id)) {
            if ((int)$this->item['productId'] == (int)$product_id) {
                $this->wish = !is_null($this->client) && ($this->client->isWish($product_id));
            }
        } else {
            $this->wish = !is_null($this->client) && ($this->client->isWish($this->item['productId']));
        }
    }

    public function toggle_wish(): void
    {
        if (!is_null($this->client)) {
            $service = new WishService();
            $service->toggle($this->client->id, (int)$this->item['productId']);
            $this->update_wish();
            $this->dispatch('update-header-wish');
        }
    }

    public function sub_item(SubToCartUseCase $useCase): void
    {
        $useCase->execute($this->item['productId'], 1);

        $this->dispatch('update-header-cart');
        $this->dispatch('update-item-cart')->self();
        $this->dispatch('e-cart', product_id: $this->item['productId'], e_type: 'remove', quantity: 1);
    }

    public function plus_item(PlusToCartUseCase $useCase): void
    {
        $useCase->execute($this->item['productId'], 1);
       // $this->cart->plus($this->item['productId'], 1);
        $this->dispatch('update-header-cart');
        $this->dispatch('update-item-cart')->self();
        $this->dispatch('e-cart', product_id: $this->item['productId'], e_type: 'add', quantity: 1);
    }

    public function set_item(SetToCartUseCase $useCase): void
    {
        $result = $useCase->execute($this->item['productId'], $this->quantity);

        if ($result > 0)
            $this->dispatch('e-cart', product_id: $this->item['productId'], e_type: 'add', quantity: $result);

        if ($result < 0)
            $this->dispatch('e-cart', product_id: $this->item['productId'], e_type: 'remove', quantity: -1 * $result);

        if ($result = 0)
            $this->dispatch('e-cart', product_id: $this->item['productId'], e_type: 'remove', quantity: $this->quantity);


        $this->dispatch('update-header-cart');
        $this->dispatch('update-item-cart')->self();
    }

    public function check_item(CheckToCartUseCase  $useCase): void
    {
        $useCase->execute($this->item['productId']);
        //$this->cart->check($this->item['productId']);
        $this->dispatch('update-header-cart');
    }

    public function del_item(RemoveCartItemUseCase $useCase): void
    {
        $this->dispatch('e-cart', product_id: $this->item['productId'], e_type: 'remove', quantity: $this->quantity);
        $useCase->execute($this->item['productId']);
        //$this->cart->remove($this->item['productId']);
        $this->dispatch('update-header-cart');
    }

    public function render()
    {
        return view('livewire.cabinet.cart.cart-item');
    }
}
