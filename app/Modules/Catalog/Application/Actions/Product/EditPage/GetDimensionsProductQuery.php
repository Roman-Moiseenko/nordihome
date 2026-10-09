<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Catalog\Application\DTOs\Product\EditPage\ViewDimensionsProductData;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;

final readonly class GetDimensionsProductQuery
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    public function execute(int $id): ViewDimensionsProductData
    {
        return ViewDimensionsProductData::fromEntity(
            $this->productRepository->getById($id),
        );
    }
}
