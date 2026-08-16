<?php

namespace App\Modules\Cart\Application\Actions;

use App\Modules\Cart\Infrastructure\Persistence\HybridStorage;

class RemoveCartItemUseCase
{
    public function __construct(
        private HybridStorage $storage
    )
    {

    }
    public function execute(int $itemId)
    {
        $this->storage->remove($itemId);
    }
}
