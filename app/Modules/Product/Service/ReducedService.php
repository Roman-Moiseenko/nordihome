<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Product\Entity\Product;

class ReducedService
{
    public function setReducedProduct(int $product_id): void
    {
        $product = Product::find($product_id);
        $product->setReduced(true);
    }

    public function setReducedProducts(array $products): void
    {
        foreach ($products as $product) {
            $this->setReducedProduct($product['product_id']);
        }
    }

    public function delReducedProduct($product): void
    {
        $product->setReduced(false);
    }
}
