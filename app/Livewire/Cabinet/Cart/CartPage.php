<?php

namespace App\Livewire\Cabinet\Cart;

use App\Modules\Analytics\Domain\ValueObjects\ActionType;
use App\Modules\Analytics\Domain\ValueObjects\EntityType;
use App\Modules\Analytics\Presentation\Support\RecordsAnalyticsAction;
use App\Modules\Cart\Application\Actions\CheckAllToCartUseCase;
use App\Modules\Cart\Application\Actions\GetCartUseCase;
use App\Modules\Cart\Application\Actions\RemoveCartItemUseCase;
use App\Modules\Shop\Application\DTOs\ClientContext;
use App\Modules\Shop\Application\Services\ClientContextFactory;
use App\Modules\Shop\Presentation\Http\Middlewares\InjectClientContextMiddleware;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Livewire;

class CartPage extends Component
{
    use RecordsAnalyticsAction;

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

    public ?int $clientId = null;
    public function boot()
    {

    }

    /**
     * @throws BindingResolutionException
     */
    public function mount(?int $clientId, bool $preorder = false)
    {
        $this->clientId = $clientId;
        $this->refresh_data();
        $this->check_preorder = $preorder;
    }

    /**
     * @throws BindingResolutionException
     */
    #[On('update-header-cart')]
    public function refresh_data(): void
    {
        $context = app(ClientContextFactory::class)->make();
        $this->renderKey++;
        $useCase = app()->make(GetCartUseCase::class);
        $data = $useCase->execute($context);

        $this->items = json_decode(json_encode($data->items), true);

        $this->amount = $data->amount;
        $this->discount = $data->discount;
        $this->quantity = $data->quantity;

        $this->amountCheck = $data->amountCheck;
        $this->discountCheck = $data->discountCheck;
        $this->quantityCheck = $data->quantityCheck;
        $this->delivery = $data->delivery;
        $this->deliveryParser = $data->deliveryParser;

        $this->check_all = true;
        $this->button_trash = false;
        foreach ($data->items as $item) {
            if ($item->check) $this->button_trash = true;
            if (!$item->check) $this->check_all = false;
        }

    }

    public function check_items(CheckAllToCartUseCase $useCase)
    {
        $context = app(ClientContextFactory::class)->make();
        $useCase->execute($this->check_all, $context);
        $this->dispatch('update-header-cart');

    }

    public function del_select(GetCartUseCase $cartUseCase, RemoveCartItemUseCase $useCase): void
    {
        $context = app(ClientContextFactory::class)->make();
        $items = $cartUseCase->execute($context)->items;
        foreach ($items as $item) {
            if ($item->check) {
                $this->dispatch('e-cart', product_id: $item->productId, e_type: 'remove', quantity: $item->quantity);
                $useCase->execute($item->productId, $context);

                $this->recordAnalyticsAction(
                    ActionType::CART_REMOVE,
                    EntityType::PRODUCT,
                    (int)$item->productId,
                    ['quantity' => $item->quantity],
                );
            }
        }

        $this->dispatch('update-header-cart');
    }

    public function render()
    {
        return view('livewire.cabinet.cart.cart-page');
    }
}
