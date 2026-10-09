<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product\EditPage;

use App\Modules\Catalog\Domain\Entities\ProductEntity;

class ViewDescriptionProductData
{
    /**
     * @param int[] $tags ID тегов товара
     */
    public function __construct(
        public int $id,
        public string $description,
        public string $short,
        public string $care,
        public string $model,
        public array $tags,
        public ?int $seriesId,
        public bool $hasModification,
    )
    {
    }

    /**
     * @param int[] $tagIds
     */
    public static function fromEntity(ProductEntity $entity, array $tagIds = []): self
    {
        return new self(
            id: $entity->id,
            description: $entity->description,
            short: $entity->short,
            care: $entity->care,
            model: $entity->model,
            tags: array_values(array_map('intval', $tagIds)),
            seriesId: $entity->seriesId,
            hasModification: $entity->hasModification,
        );
    }
}
