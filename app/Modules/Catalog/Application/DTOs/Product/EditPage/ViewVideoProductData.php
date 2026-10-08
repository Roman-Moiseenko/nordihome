<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product\EditPage;

use App\Modules\Catalog\Domain\Entities\ProductEntity;

class ViewVideoProductData
{
    /**
     * @param array<int, array{id: int, url: string, caption: string, description: string}> $videos
     */
    public function __construct(
        public int $id,
        public array $videos,
        public bool $hasModification,
    )
    {
    }

    /**
     * @param array<int, array{id: int, url: string, caption: string, description: string}> $videos
     */
    public static function fromEntity(ProductEntity $entity, array $videos = []): self
    {
        return new self(
            id: $entity->id,
            videos: $videos,
            hasModification: $entity->hasModification,
        );
    }
}
