<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Service;

use App\Modules\Catalog\Infrastructure\Models\Product;

//TODO Переделать на UseCase
class OnOrderService
{
    public function setOnOrderProduct(int $product_id): void
    {
        /** @var Product $product */
        $product = Product::find($product_id);
        $product->only_on_order = true;
        $product->save();

    }

    public function setOnOrderProducts(array $products): void
    {
        foreach ($products as $product) {
            $this->setOnOrderProduct($product['product_id']);
        }
    }

    public function delOnOrderProduct($product): void
    {
        $product->only_on_order = false;
        $product->save();
    }
}
