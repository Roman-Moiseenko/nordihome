<?php

namespace App\Livewire\Header;

use App\Modules\Analytics\Domain\ValueObjects\ActionType;
use App\Modules\Analytics\Domain\ValueObjects\EntityType;
use App\Modules\Analytics\Presentation\Support\RecordsAnalyticsAction;
use App\Modules\Cart\Application\Actions\ClearCartUseCase;
use App\Modules\Cart\Application\Actions\GetCartUseCase;
use App\Modules\Cart\Application\Actions\RemoveCartItemUseCase;
use App\Modules\Cart\Application\DTOs\CartItemData;
use App\Modules\Shop\Application\Services\ClientContextFactory;
use Illuminate\Contracts\Container\BindingResolutionException;
use Livewire\Attributes\On;
use Livewire\Component;

class Cart extends Component
{
    use RecordsAnalyticsAction;
    public string $test = '';
    public int $count;
    public float $amount;
    public float $discount;
    public ?int $clientId = null;

    /** @var CartItemData[] $items  */
    public array $items;

    public function boot(): void
    {
    }


    public function mount(?int $clientId): void
    {
        $this->clientId = $clientId;
        $this->refresh_fields();
    }


    #[On('update-header-cart')]
    public function refresh_fields(): void
    {

        $useCase = app()->make(GetCartUseCase::class);
        $context = app(ClientContextFactory::class)->make();
        $data = $useCase->execute($context);

        $this->items = json_decode(json_encode($data->items), true);
        $this->amount = $data->amount;
        $this->discount = $data->discount;
        $this->count = $data->quantity;
    }

    public function del_item($id, RemoveCartItemUseCase $useCase): void
    {
        $context = app(ClientContextFactory::class)->make();
        $quantity = $useCase->execute($id, $context);

        $this->recordAnalyticsAction(
            ActionType::CART_REMOVE,
            EntityType::PRODUCT,
            (int)$id,
            ['quantity' => $quantity],
        );

        $this->dispatch('e-cart', product_id: $id, e_type: 'remove', quantity: $quantity);
        $this->dispatch('update-header-cart');
    }

    /**
     * @throws BindingResolutionException
     */
    public function clear_cart(GetCartUseCase $cartUseCase, ClearCartUseCase $clearUseCase): void
    {
        $context = app(ClientContextFactory::class)->make();
        $items = $cartUseCase->execute($context)->items;
        foreach ($items as $item) {
            $this->dispatch('e-cart',
                product_id: $item->productId, e_type: 'remove', quantity: $item->quantity);
        }
        $clearUseCase->execute($context);

        $this->recordAnalyticsAction(ActionType::CART_CLEAR);

        $this->refresh_fields();
        $this->dispatch('update-header-cart');
    }

    public function render()
    {
        return view('livewire.header.cart');
    }
}
