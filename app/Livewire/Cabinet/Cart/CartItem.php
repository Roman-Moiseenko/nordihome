<?php

namespace App\Livewire\Cabinet\Cart;

use App\Modules\User\Entity\User;
use App\Modules\User\Service\WishService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartItem extends Component
{

    /**
     * @var \App\Modules\Shop\Cart\Cart|mixed
     */
    private mixed $cart;
    public array $item;
    public int $quantity;
    public User|null $user;

    public bool $wish;
    public bool $check;

    public function boot(): void
    {
        $this->cart = app()->make('\App\Modules\Shop\Cart\Cart');
    }

    public function mount(array $item, mixed $user): void
    {
        $this->item = $item;
        $this->quantity = $item['quantity'];
        $this->check = $item['check'];
        $this->user = $user;
        $this->update_wish();
        $this->wish = !is_null($this->user) && ($this->user->isWish($this->item['product_id']));
    }

    #[On('update-item-cart')]
    public function refresh_data(): void
    {
        $this->cart->loadItems();
        $this->item = $this->cart->ItemData($this->cart->getItem($this->item['product_id']));
        $this->quantity = $this->item['quantity'];
        $this->check = $this->item['check'];
    }

    #[On('update-wish')]
    public function update_wish($product_id = null): void
    {
        if (!is_null($product_id)) {
            if ((int)$this->item['product_id'] == (int)$product_id) {
                $this->wish = !is_null($this->user) && ($this->user->isWish($product_id));
            }
        } else {
            $this->wish = !is_null($this->user) && ($this->user->isWish($this->item['product_id']));
        }
    }

    public function toggle_wish(): void
    {
        if (!is_null($this->user)) {
            $service = new WishService();
            $service->toggle($this->user->id, (int)$this->item['product_id']);
            $this->update_wish();
            $this->dispatch('update-header-wish');
        }
    }

    public function sub_item(): void
    {
        $this->cart->sub($this->item['product_id'], 1);
        $this->dispatch('update-header-cart');
        $this->dispatch('update-item-cart')->self();
        $this->dispatch('e-cart', product_id: $this->item['product_id'], e_type: 'remove', quantity: 1);
    }

    public function plus_item(): void
    {
        $this->cart->plus($this->item['product_id'], 1);
        $this->dispatch('update-header-cart');
        $this->dispatch('update-item-cart')->self();
        $this->dispatch('e-cart', product_id: $this->item['product_id'], e_type: 'add', quantity: 1);
    }

    public function set_item(): void
    {
        $old_quantity = $this->item['quantity'];
        if ($old_quantity < $this->quantity) {
            $this->dispatch('e-cart',
                product_id: $this->item['product_id'],
                e_type: 'add',
                quantity: $this->quantity - $old_quantity);
        } else {
            $this->dispatch('e-cart',
                product_id: $this->item['product_id'],
                e_type: 'remove',
                quantity: $old_quantity - $this->quantity);
        }

        $this->cart->set($this->item['product_id'], $this->quantity);
        $this->dispatch('update-header-cart');
        $this->dispatch('update-item-cart')->self();
    }

    public function check_item(): void
    {
        $this->cart->check($this->item['product_id']);
        $this->dispatch('update-header-cart');
    }

    public function del_item(): void
    {
        $this->dispatch('e-cart', product_id: $this->item['product_id'], e_type: 'remove', quantity: $this->quantity);
        $this->cart->remove($this->item['product_id']);
        $this->dispatch('update-header-cart');
    }

    public function render()
    {
        return view('livewire.cabinet.cart.cart-item');
    }
}
