<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Catalog\Application\DTOs\Product\EditPage\ViewDescriptionProductData;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\TagProductRepositoryInterface;

final readonly class GetDescriptionProductQuery
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private TagProductRepositoryInterface $tagProductRepository,
    ) {
    }

    public function execute(int $id): ViewDescriptionProductData
    {
        $entity = $this->productRepository->getById($id);

        return ViewDescriptionProductData::fromEntity(
            $entity,
            tagIds: $this->tagProductRepository->getTagsByProductId($id),
        );
    }
}
