<?php

namespace App\Livewire\Shop\Products;

use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\User;
use App\Modules\User\Service\WishService;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductCard extends Component
{
    public Product $product;
    public ?User $user;
    public bool $is_wish;

    public function mount(Product $product, mixed $user)
    {
        $this->product = $product;
        $this->user = $user;
        $this->is_wish = !is_null($user) && $product->isWish($user->id);
    }

    public function toggle_wish()
    {
        if (!is_null($this->user)) {
            $service = new WishService();
            $service->toggle($this->user->id, $this->product->id);
            $this->is_wish = !$this->is_wish;
            $this->dispatch('update-header-wish');
        }
    }

    #[On('update-wish')]
    public function update_wish($product_id = null)
    {
        if (!is_null($product_id)) {
            if ($this->product->id == (int)$product_id) {
                $this->is_wish = !is_null($this->user) && ($this->user->isWish($product_id));
            }
        } else {
            $this->is_wish = !is_null($this->user) && ($this->user->isWish($this->product->id));
        }
    }

    public function to_cart()
    {
        $cart = app()->make('\App\Modules\Shop\Cart\Cart');
        $cart->add($this->product, 1, []);
        $this->dispatch('update-header-cart');
    }

    public function render()
    {
        return view('livewire.shop.products.product-card');
    }
}
