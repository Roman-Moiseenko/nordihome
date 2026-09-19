<?php

namespace App\Modules\Cart\Application\Actions;


use App\Modules\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Modules\Storefront\Application\DTOs\ClientContext;

readonly class SubToCartUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    )
    {
    }
    public function execute(int $productId, int $quantity, ClientContext $client): void
    {
        $item = $this->cartRepository->getItemByProductId($productId, $client);

        if ($item->quantity <= $quantity) {
            $this->cartRepository->removeByProductId($productId, $client);
        } else {
            $item->quantity -= $quantity;
            $this->cartRepository->save($item, $client);
        }
    }
}
