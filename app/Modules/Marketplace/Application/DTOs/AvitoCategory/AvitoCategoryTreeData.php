<?php

namespace App\Modules\Marketplace\Application\DTOs\AvitoCategory;

use App\Modules\Marketplace\Domain\Entities\AvitoCategoryEntity;
use Spatie\LaravelData\Data;

class AvitoCategoryTreeData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly int $depth,
        public readonly int $fieldCount = 0,
        /** @var AvitoCategoryTreeData[] */
        public readonly array $children = [],
    ) {}

    /**
     * @param array<int, int> $fieldCounts map avito_category_id => count
     */
    public static function fromEntity(AvitoCategoryEntity $entity, array $fieldCounts = []): self
    {
        return new self(
            id: $entity->id,
            name: $entity->name,
            slug: $entity->slug,
            depth: $entity->depth,
            fieldCount: $fieldCounts[$entity->id] ?? 0,
            children: array_map(
                fn(AvitoCategoryEntity $child) => self::fromEntity($child, $fieldCounts),
                $entity->children
            ),
        );
    }
}
