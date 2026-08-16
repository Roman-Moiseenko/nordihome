<?php

namespace App\Modules\Cart\Application\Actions;

use App\Modules\Cart\Application\DTOs\AddProductToCartData;
use App\Modules\Cart\Domain\Entities\CartItem;
use App\Modules\Cart\Infrastructure\Persistence\HybridStorage;
use Illuminate\Contracts\Container\BindingResolutionException;

readonly class AddToCartUseCase
{

    public function __construct(
        private HybridStorage $storage
    )
    {

    }

    /**
     * @throws BindingResolutionException
     */
    public function execute(AddProductToCartData $dto): void
    {

        $items = $this->storage->load();

        foreach ($items as $current) {
            if ($current->isProduct($dto->id)) {
                $this->storage->plus($current, $dto->quantity);
                return;
            }
        }

        $this->storage->add(CartItem::create($dto->id, $dto->quantity, $dto->isParser));

    }
}
