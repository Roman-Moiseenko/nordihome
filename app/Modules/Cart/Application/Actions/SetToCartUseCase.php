<?php

namespace App\Modules\Cart\Application\Actions;

use App\Modules\Cart\Application\DTOs\UpdateProductCartData;
use App\Modules\Cart\Infrastructure\Persistence\HybridStorage;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Возвращает ситуацию для e-cart, 0 - удаленно
 * >0 сколько добавили, <0 сколько отняли
 */
class SetToCartUseCase
{
    public function __construct(
        private HybridStorage $storage
    )
    {
    }

    /**
     * @throws BindingResolutionException
     */
    public function execute(int $productId, int $quantity): int
    {
        $items = $this->storage->load();
        //Удаляем при кол-ве = 0
        if ($quantity == 0) {
            foreach ($items as $current) {
                if ($current->isProduct($productId)) {
                    $this->storage->remove($current->id);
                    return 0;
                }
            }
        }

        foreach ($items as $current) {
            if ($current->isProduct($productId)) {
                $newValue = $quantity - $current->quantity;
                if ($newValue > 0) $this->storage->plus($current, $newValue);
                if ($newValue < 0) $this->storage->sub($current, -1 * $newValue);
                return $newValue;
            }
        }
        return 0;
    }
}
