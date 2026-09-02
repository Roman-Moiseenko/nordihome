<?php

namespace App\Modules\Cabinet\Application\Actions\Wish;

class RemoveWishUseCase
{
    public function execute(int $wishId): int
    {
        $wish = \App\Modules\Cabinet\Infrastructure\Models\Wish::find($wishId);
        $productId = $wish->product_id;
        $wish->delete();
        return $productId;
    }
}
