<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Product\Entity\Product;

class PriorityService
{
    public function setPriorityProduct(int $product_id): void
    {
        $product = Product::find($product_id);
        $product->setPriority(true);
    }

    public function setPriorityProducts(array $codes): void
    {
        foreach ($codes as $code) {
            $product = Product::where('code', trim($code))->first();
            $product->setPriority(true);
        }
    }

    public function delPriorityProduct($product): void
    {
        $product->setPriority(false);
    }
}
