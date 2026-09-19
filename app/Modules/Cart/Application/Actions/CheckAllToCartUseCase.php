<?php

namespace App\Modules\Cart\Application\Actions;

use App\Modules\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Modules\Storefront\Application\DTOs\ClientContext;

/**
 * Отмечаем или снимаем отметку со всех товаров в корзине
 */
readonly class CheckAllToCartUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    )
    {
    }

    public function execute(bool $checked, ClientContext $client): void
    {

        $items = $this->cartRepository->getAll($client);
        foreach ($items as $item) {
            $item->check = $checked;
            $this->cartRepository->save($item, $client);
        }
    }
}
