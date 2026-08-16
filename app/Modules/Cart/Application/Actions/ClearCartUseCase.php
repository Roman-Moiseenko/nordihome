<?php

namespace App\Modules\Cart\Application\Actions;

use App\Modules\Cart\Application\DTOs\UpdateProductCartData;
use App\Modules\Cart\Infrastructure\Persistence\HybridStorage;
use Illuminate\Contracts\Container\BindingResolutionException;

class ClearCartUseCase
{
    public function __construct(
        private HybridStorage $storage
    )
    {
    }

    /**
     * @throws BindingResolutionException
     */
    public function execute(): void
    {
        $this->storage->clear();
    }
}
