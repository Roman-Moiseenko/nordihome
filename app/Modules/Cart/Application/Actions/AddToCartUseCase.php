<?php

namespace App\Modules\Cart\Application\Actions;

use App\Modules\Cart\Application\DTOs\AddProductToCartData;
use App\Modules\Cart\Domain\Entities\CartItemEntity;
use App\Modules\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Modules\Shop\Application\DTOs\ClientContext;

readonly class AddToCartUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    )
    {
    }

    public function execute(AddProductToCartData $dto, ClientContext $client): void
    {

        $item = $this->cartRepository->getItemByProductId($dto->id, $client);


        if (!is_null($item)) {
            $item->quantity += $dto->quantity;
        } else {
            $item = new CartItemEntity(
                productId: $dto->id,
                quantity: $dto->quantity,
                isParser: $dto->isParser
            );
        }
        $this->cartRepository->save($item, $client);

    }
}
