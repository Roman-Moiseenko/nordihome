<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Catalog\Application\DTOs\Product\EditPage\ViewVideoProductData;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\VideoRepositoryInterface;

final readonly class GetVideoProductQuery
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private VideoRepositoryInterface $videoRepository,
    ) {
    }

    public function execute(int $id): ViewVideoProductData
    {
        $entity = $this->productRepository->getById($id);

        return ViewVideoProductData::fromEntity(
            $entity,
            videos: $this->videoRepository->getByProductId($id),
        );
    }
}
