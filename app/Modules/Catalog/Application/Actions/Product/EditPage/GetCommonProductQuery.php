<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Catalog\Application\DTOs\Product\EditPage\ViewCommonProductData;
use App\Modules\Catalog\Domain\Interfaces\CategoryProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\RoomProductRepositoryInterface;

final readonly class GetCommonProductQuery
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CategoryProductRepositoryInterface $categoryProductRepository,
        private RoomProductRepositoryInterface $roomProductRepository,
    ) {
    }

    public function execute(int $id): ViewCommonProductData
    {
        $entity = $this->productRepository->getById($id);

        return ViewCommonProductData::fromEntity(
            $entity,
            categoryIds: $this->categoryProductRepository->getCategoriesByProductId($id),
            roomIds: $this->roomProductRepository->getRoomsByProductId($id),
        );
    }
}
