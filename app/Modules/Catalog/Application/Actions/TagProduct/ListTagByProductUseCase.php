<?php

namespace App\Modules\Catalog\Application\Actions\TagProduct;

use App\Modules\Catalog\Application\DTOs\Tag\TagViewData;
use App\Modules\Catalog\Application\Interfaces\TagProductRepositoryInterface;
use App\Modules\Catalog\Application\Interfaces\TagRepositoryInterface;

class ListTagByProductUseCase
{
    public function __construct(
        private TagProductRepositoryInterface $tagProductRepository,
        private TagRepositoryInterface $tagRepository
    )
    {
    }

    public function execute(int $productId): array
    {
        $roomIds = $this->tagProductRepository->getTagsByProductId($productId);

        if (empty($roomIds)) return [];

        $tags = $this->tagRepository->findByIds($roomIds);

        return array_map(
            fn($room) => TagViewData::fromEntity($room),
            $tags
        );
    }
}
