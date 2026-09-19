<?php

namespace App\Modules\Cart\Application\Actions;

use App\Modules\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Modules\Shop\Application\DTOs\ClientContext;

readonly class CheckToCartUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    )
    {
    }

    public function execute(int $id, ClientContext $client): void
    {
        $item = $this->cartRepository->getItemByProductId($id, $client);
        $item->check = !$item->check;
        $this->cartRepository->save($item, $client);

    }
}
