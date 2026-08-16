<?php

namespace App\Modules\Cart\Application\Actions;

use App\Modules\Cart\Application\DTOs\UpdateProductCartData;
use App\Modules\Cart\Infrastructure\Persistence\HybridStorage;
use Illuminate\Contracts\Container\BindingResolutionException;

class CheckToCartUseCase
{
    public function __construct(
        private HybridStorage $storage
    )
    {
    }

    /**
     * @throws BindingResolutionException
     */
    public function execute(int $id): void
    {
        $items = $this->storage->load();
        foreach ($items as $current) {
            if ($current->isProduct($id)) {
                $current->check();
                $this->storage->check($current);
                return;
            }
        }
    }
}
