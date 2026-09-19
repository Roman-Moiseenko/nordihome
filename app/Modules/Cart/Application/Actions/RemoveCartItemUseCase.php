<?php

namespace App\Modules\Cart\Application\Actions;

use App\Modules\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Modules\Storefront\Application\DTOs\ClientContext;

/**
 * Возвращает кол-во удаленных
 */
readonly class RemoveCartItemUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    )
    {

    }
    public function execute(int $productId, ClientContext $client): int
    {
        $this->cartRepository->removeByProductId($productId, $client);

        return 0;
    }
}
