<?php

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\BalanceProduct;
use App\Modules\Product\Entity\Product;

class BalanceProductService
{
    public function setBalance(Product $product, int $min, int $max = null, $buy = true): void
    {
        if ($product->balance()->count() == 0) {

            $balance = BalanceProduct::new($min, $max, $buy);
            $product->balance()->save($balance);
        } else {
            $product->balance()->update([
                'min' => $min,
                'max' => $max,
                'buy' => $buy,
            ]);
        }
    }
}
