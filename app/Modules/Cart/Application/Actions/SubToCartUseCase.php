<?php

namespace App\Modules\Cart\Application\Actions;

use App\Modules\Cart\Application\DTOs\UpdateProductCartData;
use App\Modules\Cart\Infrastructure\Persistence\HybridStorage;

class SubToCartUseCase
{
    public function __construct(
        private HybridStorage $storage
    )
    {
    }
    public function execute(int $productId, int $quantity): void
    {
        $items = $this->storage->load();
        foreach ($items as $current) {
            if ($current->isProduct($productId)) {
                $this->storage->sub($current, $quantity);
                return;
            }
        }
    }
}
