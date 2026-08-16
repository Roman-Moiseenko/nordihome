<?php

namespace App\Livewire\Cabinet\Cart;

use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\User\Service\WishService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartItem extends Component
{

    /**
     * @var \App\Modules\Cart\Domain\Entities\Cart|mixed
     */
    private mixed $cart;
    public array $item;
    public int $quantity;
    private Client|null $client;

    public bool $wish;
    public bool $check;

    public function boot(): void
    {
        $this->cart = app()->make('\App\Modules\Cart\Domain\Entities\Cart');
    }

    public function mount(array $item, int|null $clientId): void
    {
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

    public function sub_item(): void
    {
        $this->cart->sub($this->item['productId'], 1);
        $this->dispatch('update-header-cart');
        $this->dispatch('update-item-cart')->self();
        $this->dispatch('e-cart', product_id: $this->item['productId'], e_type: 'remove', quantity: 1);
    }

    public function plus_item(): void
    {
        $this->cart->plus($this->item['productId'], 1);
        $this->dispatch('update-header-cart');
        $this->dispatch('update-item-cart')->self();
        $this->dispatch('e-cart', product_id: $this->item['productId'], e_type: 'add', quantity: 1);
    }

    public function set_item(): void
    {
        $old_quantity = $this->item['quantity'];
        if ($old_quantity < $this->quantity) {
            $this->dispatch('e-cart',
                product_id: $this->item['productId'],
                e_type: 'add',
                quantity: $this->quantity - $old_quantity);
        } else {
            $this->dispatch('e-cart',
                product_id: $this->item['productId'],
                e_type: 'remove',
                quantity: $old_quantity - $this->quantity);
        }

        $this->cart->set($this->item['productId'], $this->quantity);
        $this->dispatch('update-header-cart');
        $this->dispatch('update-item-cart')->self();
    }

    public function check_item(): void
    {
        $this->cart->check($this->item['productId']);
        $this->dispatch('update-header-cart');
    }

    public function del_item(): void
    {
        $this->dispatch('e-cart', product_id: $this->item['productId'], e_type: 'remove', quantity: $this->quantity);
        $this->cart->remove($this->item['productId']);
        $this->dispatch('update-header-cart');
    }

    public function render()
    {
        return view('livewire.cabinet.cart.cart-item');
    }
}
