<?php

namespace App\Livewire\Cabinet\Cart;

use App\Modules\Shop\Application\Actions\Cart\GetCartUseCase;
use App\Modules\User\Entity\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Livewire\Attributes\On;
use Livewire\Component;

use \App\Modules\Shop\Cart\Cart as CartEntity;

class CartPage extends Component
{

    private CartEntity $cart;

   // public Product $product;

    public array $items = [];
    public int $quantity;
    public float $amount;
    public float $discount;
    public int $quantityCheck;
    public float $amountCheck;
    public float $discountCheck;
    public float $delivery;
    public float $deliveryParser;

    public bool $preorder;
    public bool $button_trash;
    public bool $check_all;
    public bool $check_preorder;

    public int $renderKey = 0; // счётчик изменений
    public function boot()
    {
        $this->cart = app()->make('\App\Modules\Shop\Cart\Cart');
    }

    public function mount(bool $preorder = false)
    {

        $this->refresh_data();
        $this->check_preorder = $preorder;
    }

    /**
     * @throws BindingResolutionException
     */
    #[On('update-header-cart')]
    public function refresh_data(): void
    {
        $this->renderKey++;
        $useCase = app()->make(GetCartUseCase::class);
        $data = $useCase->execute();
        $this->items = json_decode(json_encode($data->items), true);

        //$this->cart->loadItems();
        //$this->items = $this->cart->ItemsData($this->cart->getItems());

        $this->amount = $data->amount;
        $this->discount = $data->discount;
        $this->quantity = $data->quantity;

        $this->amountCheck = $data->amountCheck;
        $this->discountCheck = $data->discountCheck;
        $this->quantityCheck = $data->quantityCheck;
        $this->delivery = $data->delivery;
        $this->deliveryParser = $data->deliveryParser;
        //$this->preorder = $this->cart->info->preorder;
        //$this->check_all = $this->cart->info->check_all;

        $this->check_all = true;
        $this->button_trash = false;
        foreach ($data->items as $item) {
            if ($item->check) $this->button_trash = true;
            if (!$item->check) $this->check_all = false;
        }

    }

    public function check_items()
    {
        $this->cart->check_all($this->check_all);

        $this->dispatch('update-header-cart');
      //  $this->dispatch('update-item-cart');
    }

    public function del_select(): void
    {
        $items = $this->cart->get_check_items();
        foreach ($items as $item) {
            $this->dispatch('e-cart', product_id: $item['product_id'], e_type: 'remove', quantity: $item['quantity']);
        }

        $this->cart->clear_check();
        $this->dispatch('update-header-cart');
    }

    public function render()
    {
        return view('livewire.cabinet.cart.cart-page');
    }
}
