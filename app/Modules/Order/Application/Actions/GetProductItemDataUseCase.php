<?php
declare(strict_types=1);

namespace App\Modules\Order\Application\Actions;

use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Order\Application\DTOs\ProductItemData;

class GetProductItemDataUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    )
    {
    }

    public function execute(int $productId): ProductItemData
    {
        $product = $this->productRepository->getById($productId);

        $volume = 0.0;
        $weight = 0.0;

        if ($product->dimensions !== null) {
            $volume = $product->dimensions->volume();
            $weight = $product->dimensions->weight();
        }

        return new ProductItemData(
            id: $product->id,
            name: $product->name,
            code: $product->code->getCode(),
            volume: (string)(ceil($volume * 10000) / 10000),
            weight: (string)(ceil($weight * 1000) / 1000),
        );
    }
}
