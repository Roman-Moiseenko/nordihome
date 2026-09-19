<?php

namespace App\Livewire\Cabinet\Cart;

use App\Modules\Analytics\Domain\ValueObjects\ActionType;
use App\Modules\Analytics\Domain\ValueObjects\EntityType;
use App\Modules\Analytics\Presentation\Support\RecordsAnalyticsAction;
use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Cart\Application\Actions\CheckToCartUseCase;
use App\Modules\Cart\Application\Actions\PlusToCartUseCase;
use App\Modules\Cart\Application\Actions\RemoveCartItemUseCase;
use App\Modules\Cart\Application\Actions\SetToCartUseCase;
use App\Modules\Cart\Application\Actions\SubToCartUseCase;
use App\Modules\Storefront\Application\Services\ClientContextFactory;
use App\Modules\User\Service\WishService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Livewire\Attributes\On;
use Livewire\Component;

class CartItem extends Component
{
    use RecordsAnalyticsAction;

 //   private mixed $cart;
    public array $item;
    public int $quantity;
    public ?Client $client = null;

    public bool $wish;
    public bool $check;
   // public array $context = [];

    public function boot(): void
    {
    }

    public function mount(array $item, int|null $clientId): void
    {
        $this->item = $item;
        $this->quantity = $item['quantity'];
        $this->check = $item['check'];
        $this->client = is_null($clientId) ? null : Client::find($clientId);
        $this->update_wish();
        //$this->wish = !is_null($this->client) && ($this->client->isWish($this->item['productId']));
    }

    #[On('update-item-cart')]
    public function refresh_data(): void
    {
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
            $wasWish = $this->wish;

            $service = new WishService();
            $service->toggle($this->client->id, (int)$this->item['productId']);
            $this->update_wish();
            $this->dispatch('update-header-wish');

            $this->recordAnalyticsAction(
                $wasWish ? ActionType::WISHLIST_REMOVE : ActionType::WISHLIST_ADD,
                EntityType::PRODUCT,
                (int)$this->item['productId'],
            );
        }
    }

    public function sub_item(SubToCartUseCase $useCase): void
    {
        $context = app(ClientContextFactory::class)->make();
        $useCase->execute($this->item['productId'], 1, $context);

        $this->recordAnalyticsAction(
            ActionType::CART_QUANTITY_CHANGE,
            EntityType::PRODUCT,
            (int)$this->item['productId'],
            ['delta' => -1],
        );

        $this->dispatch('update-header-cart');
        $this->dispatch('update-item-cart')->self();
        $this->dispatch('e-cart', product_id: $this->item['productId'], e_type: 'remove', quantity: 1);
    }


    public function plus_item(PlusToCartUseCase $useCase): void
    {
        $context = app(ClientContextFactory::class)->make();
        $useCase->execute($this->item['productId'], 1, $context);

        $this->recordAnalyticsAction(
            ActionType::CART_QUANTITY_CHANGE,
            EntityType::PRODUCT,
            (int)$this->item['productId'],
            ['delta' => 1],
        );

        $this->dispatch('update-header-cart');
        $this->dispatch('update-item-cart')->self();
        $this->dispatch('e-cart', product_id: $this->item['productId'], e_type: 'add', quantity: 1);
    }

    /**
     * @throws BindingResolutionException
     */
    public function set_item(SetToCartUseCase $useCase): void
    {
        $context = app(ClientContextFactory::class)->make();
        $result = $useCase->execute($this->item['productId'], $this->quantity, $context);

        $this->recordAnalyticsAction(
            ActionType::CART_QUANTITY_CHANGE,
            EntityType::PRODUCT,
            (int)$this->item['productId'],
            ['quantity' => $this->quantity],
        );

        if ($result > 0)
            $this->dispatch('e-cart', product_id: $this->item['productId'], e_type: 'add', quantity: $result);

        if ($result < 0)
            $this->dispatch('e-cart', product_id: $this->item['productId'], e_type: 'remove', quantity: -1 * $result);

        if ($result = 0)
            $this->dispatch('e-cart', product_id: $this->item['productId'], e_type: 'remove', quantity: $this->quantity);


        $this->dispatch('update-header-cart');
        $this->dispatch('update-item-cart')->self();
    }

    /**
     * @throws BindingResolutionException
     */
    public function check_item(CheckToCartUseCase $useCase): void
    {
        $context = app(ClientContextFactory::class)->make();
        $useCase->execute($this->item['productId'], $context);
        $this->dispatch('update-header-cart');
    }

    public function del_item(RemoveCartItemUseCase $useCase): void
    {
        $context = app(ClientContextFactory::class)->make();
        $this->dispatch('e-cart', product_id: $this->item['productId'], e_type: 'remove', quantity: $this->quantity);
        $useCase->execute($this->item['productId'], $context);

        $this->recordAnalyticsAction(
            ActionType::CART_REMOVE,
            EntityType::PRODUCT,
            (int)$this->item['productId'],
            ['quantity' => $this->quantity],
        );

        $this->dispatch('update-header-cart');
    }

    public function render()
    {
        return view('livewire.cabinet.cart.cart-item');
    }
}
