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

    public function setPriorityProducts(array $products): void
    {
        foreach ($products as $product) {
            $this->setPriorityProduct($product['product_id']);
        }
    }

    public function delPriorityProduct($product): void
    {
        $product->setPriority(false);
    }
}
