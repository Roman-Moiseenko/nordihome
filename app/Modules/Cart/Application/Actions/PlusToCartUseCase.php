<?php

namespace App\Modules\Cart\Application\Actions;

use App\Modules\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Modules\Storefront\Application\DTOs\ClientContext;

class PlusToCartUseCase
{
    public function __construct(
        private CartRepositoryInterface $storage
    )
    {
    }


    public function execute(int $productId, int $quantity, ClientContext $client): void
    {
        $itemEntity = $this->storage->getItemByProductId($productId, $client);
        $itemEntity->quantity += $quantity;
        $this->storage->save($itemEntity, $client);

    }
}
