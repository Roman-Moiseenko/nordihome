<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Catalog\Application\DTOs\Product\EditPage\ViewAttributeProductData;
use App\Modules\Catalog\Domain\Interfaces\AttributeProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;

final readonly class GetAttributeProductQuery
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private AttributeProductRepositoryInterface $attributeProductRepository,
    ) {
    }

    public function execute(int $id): ViewAttributeProductData
    {
        $entity = $this->productRepository->getById($id);

        return ViewAttributeProductData::fromEntity(
            $entity,
            attributes: $this->attributeProductRepository->getForProduct($id),
            possibleAttributes: $this->attributeProductRepository->getPossibleForProduct($id),
        );
    }
}
