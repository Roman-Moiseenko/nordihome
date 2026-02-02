<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Product\Entity\Product;

class OnOrderService
{
    public function setOnOrderProduct(int $product_id): void
    {
        $product = Product::find($product_id);
        $product->setOnOrder(true);
    }

    public function setOnOrderProducts(array $products): void
    {
        foreach ($products as $product) {
            $this->setOnOrderProduct($product['product_id']);
        }
    }

    public function delOnOrderProduct($product): void
    {
        $product->setOnOrder(false);
    }
}
