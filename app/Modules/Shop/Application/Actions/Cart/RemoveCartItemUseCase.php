<?php

namespace App\Modules\Shop\Application\Actions\Cart;

use App\Modules\Shop\Cart\Storage\HybridStorage;

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
