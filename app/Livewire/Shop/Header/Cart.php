<?php

namespace App\Livewire\Shop\Header;

use App\Modules\Shop\Cart\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Modules\Shop\Cart\Cart as CartEntity;

class Cart extends Component
{
    private CartEntity $cart;
    public string $test = '';
    public int $count;
    public float $amount;
    public float $discount;
    //private mixed $tz;

    public array $items;

    public function boot()
    {
        $this->cart = app()->make('\App\Modules\Shop\Cart\Cart');
    }


    public function mount()
    {
        $this->cart = app()->make('\App\Modules\Shop\Cart\Cart');
        $this->refresh_fields();
    }

    #[On('update-header-cart')]
    public function refresh_fields()
    {
        $this->cart->loadItems();
        //dd(count($this->cart->getItems()));
        $this->items = array_map(function (CartItem $item) {
            return [
                'id' => $item->id,
                'image' => is_null($item->product->photo) ? $item->product->getImage() : $item->product->photo->getThumbUrl('thumb'),
                'name' => $item->product->name,
                'url' => route('shop.product.view', $item->getProduct()->slug),
                'product_id' => $item->product->id,
                'cost' => $item->base_cost * $item->getQuantity(),
                'price' => empty($item->discount_cost) ? $item->base_cost : $item->discount_cost,
                'quantity' => $item->getQuantity(),
                'discount_id' => $item->discount_id ?? null,
                'discount_cost' => empty($item->discount_cost) ? null : $item->discount_cost * $item->getQuantity(),
                'discount_name' => $item->discount_name,
            ];

        }, $this->cart->getItems());

        $this->amount = $this->cart->info->all->amount;
        $this->discount = $this->cart->info->all->discount;
        $this->count = $this->cart->info->all->count;
    }

    public function del_item($id)
    {
        $this->cart->remove($id);
        return redirect(request()->header('Referer'));
    }

    public function clear_cart()
    {
        $this->cart->clear();
        $this->refresh_fields();
    }

    public function render()
    {
        return view('livewire.shop.header.cart');
    }


}
