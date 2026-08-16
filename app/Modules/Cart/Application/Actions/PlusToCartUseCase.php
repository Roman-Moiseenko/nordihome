<?php

namespace App\Modules\Cart\Application\Actions;

use App\Modules\Cart\Application\DTOs\UpdateProductCartData;
use App\Modules\Cart\Infrastructure\Persistence\HybridStorage;
use Illuminate\Contracts\Container\BindingResolutionException;

class PlusToCartUseCase
{
    public function __construct(
        private HybridStorage $storage
    )
    {
    }

    /**
     * @throws BindingResolutionException
     */
    public function execute(int $productId, int $quantity): void
    {
        $items = $this->storage->load();
        foreach ($items as $current) {
            if ($current->isProduct($productId)) {
                $this->storage->plus($current, $quantity);
                return;
            }
        }
    }
}
