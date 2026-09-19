<?php

namespace App\Modules\Cart\Application\Actions;

use App\Modules\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Modules\Shop\Application\DTOs\ClientContext;

/**
 * Возвращает ситуацию для e-cart, 0 - удаленно
 * >0 сколько добавили, <0 сколько отняли
 */
readonly class SetToCartUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    )
    {
    }

    public function execute(int $productId, int $quantity, ClientContext $client): int
    {
        //Удаляем при кол-ве = 0
        if ($quantity == 0) {
            $this->cartRepository->removeByProductId($productId, $client);
            return 0;
        }

        $item = $this->cartRepository->getItemByProductId($productId, $client);
        $item->quantity = $quantity;
        $this->cartRepository->save($item, $client);

        return $quantity;
    }
}
