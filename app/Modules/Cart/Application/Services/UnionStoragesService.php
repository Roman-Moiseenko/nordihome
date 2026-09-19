<?php

namespace App\Modules\Cart\Application\Services;

use App\Modules\Cart\Application\Actions\AddToCartUseCase;
use App\Modules\Cart\Application\DTOs\AddProductToCartData;
use App\Modules\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Modules\Storefront\Application\DTOs\ClientContext;

readonly class UnionStoragesService
{
    public function __construct(
        private CartRepositoryInterface   $cartRepository,
        private AddToCartUseCase $addToCartUseCase,
    )
    {
    }

    public function execute(ClientContext $context): void
    {

        $items = $this->cartRepository->getItemsCookie($context);

        foreach ($items as $item) {
            $dto = new AddProductToCartData(
                id: $item->productId,
                quantity: $item->quantity,
                isParser: $item->isParser,
            );
            $this->addToCartUseCase->execute($dto, $context);

        }
        $this->cartRepository->clearCookie($context);
    }
}
