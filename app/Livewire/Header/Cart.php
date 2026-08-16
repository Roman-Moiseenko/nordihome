<?php

namespace App\Livewire\Header;

use App\Modules\Cart\Application\Actions\GetCartUseCase;
use App\Modules\Cart\Application\DTOs\CartItemData;
use App\Modules\Cart\Domain\Entities\Cart as CartEntity;
use Livewire\Attributes\On;
use Livewire\Component;

class Cart extends Component
{
    private CartEntity $cart;
    public string $test = '';
    public int $count;
    public float $amount;
    public float $discount;
    //private mixed $tz;

    /** @var CartItemData[] $items  */
    public array $items;

    public function boot(): void
    {
        $this->cart = app()->make('\App\Modules\Cart\Domain\Entities\Cart');
    }


    public function mount(): void
    {
        $this->cart = app()->make('\App\Modules\Cart\Domain\Entities\Cart');
        $this->refresh_fields();
    }


    #[On('update-header-cart')]
    public function refresh_fields(): void
    {
        $useCase = app()->make(GetCartUseCase::class);
        $data = $useCase->execute();
  //      dd($useCase->execute());
     //   $this->cart->loadItems();
        //dd(count($this->cart->getItems()));
  /*      $this->items = array_map(function (CartItem $item) {
            return [
                'id' => $item->id,
                'image' => $item->product->getImage('mini'),
                'name' => $item->product->name,
                'url' => route('shop.product.view', $item->getProduct()->slug),
                'product_id' => $item->product->id,
                'cost' => $item->base_cost * $item->quantity,
                'price' => empty($item->discount_cost) ? $item->base_cost : $item->discount_cost,
                'quantity' => $item->quantity,
                'discount_id' => $item->discount_id ?? null,
                'discount_cost' => empty($item->discount_cost) ? null : $item->discount_cost * $item->quantity,
                'discount_name' => $item->discount_name,
            ];

        }, $this->cart->getItems());
*/
        $this->items = json_decode(json_encode($data->items), true);
        $this->amount = $data->amount; // $this->cart->info->all->amount;
        $this->discount = $data->discount; //$this->cart->info->all->discount;
        $this->count = $data->quantity; //$this->cart->info->all->count;
    }

    public function del_item($id): void
    {
        $item = $this->cart->getItem($id);
        $this->dispatch('e-cart',
            product_id: $id, e_type: 'remove', quantity: $item->quantity);
        $this->cart->remove($id);
        $this->dispatch('update-header-cart');
    }

    public function clear_cart(): void
    {
        $items = $this->cart->getItems();
        foreach ($items as $item) {
            $this->dispatch('e-cart',
                product_id: $item->product->id, e_type: 'remove', quantity: $item->quantity);
        }

        $this->cart->clear();
        $this->refresh_fields();
        $this->dispatch('update-header-cart');
    }

    public function render()
    {
        return view('livewire.header.cart');
    }
}
