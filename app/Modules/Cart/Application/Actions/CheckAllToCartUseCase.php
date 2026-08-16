<?php

namespace App\Modules\Cart\Application\Actions;

use App\Modules\Cart\Application\DTOs\UpdateProductCartData;
use App\Modules\Cart\Infrastructure\Persistence\HybridStorage;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Отмечаем или снимаем отметку со всех товаров в корзине
 */
class CheckAllToCartUseCase
{
    public function __construct(
        private HybridStorage $storage
    )
    {
    }

    /**
     * @throws BindingResolutionException
     */
    public function execute(bool $checked): void
    {
        $items = $this->storage->load();
        foreach ($items as $current) {
            $current->check = $checked;
            $this->storage->check($current);

        }
    }
}
