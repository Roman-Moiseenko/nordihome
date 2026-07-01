<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Category;

use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class CategoryTreeData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $id,
        public readonly string $name,
        public readonly int $depth,
        /** @var CategoryTreeData[] */
        public readonly array $children = [],
    )
    {
    }

    /**
     * @param CategoryEntity $entity
     * @return self
     */
    public static function fromEntity(CategoryEntity $entity): self
    {
        return new self(
            id: $entity->id,
            name: $entity->name,
            depth: $entity->depth,
            children: array_map(
                fn(CategoryEntity $child) => self::fromEntity($child),
                $entity->children
            ),
        );
    }

    /**
     * @param CategoryEntity[] $entities
     * @return self[]
     */
    public static function fromEntityArray(array $entities): array
    {
        return array_map(fn(CategoryEntity $entity) => self::fromEntity($entity), $entities);
    }
}
