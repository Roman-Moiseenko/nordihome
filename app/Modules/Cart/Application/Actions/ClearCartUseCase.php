<?php

namespace App\Modules\Cart\Application\Actions;

use App\Modules\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Modules\Storefront\Application\DTOs\ClientContext;

readonly class ClearCartUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    )
    {
    }

    public function execute(ClientContext $client): void
    {
        $this->cartRepository->clearCart($client);
    }
}
