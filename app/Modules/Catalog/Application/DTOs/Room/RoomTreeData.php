<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Room;

use App\Modules\Catalog\Domain\Entities\RoomEntity;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class RoomTreeData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $id,
        public readonly string $name,
        public readonly int $depth,
        /** @var RoomTreeData[] */
        public readonly array $children = [],
    )
    {
    }

    /**
     * @param RoomEntity $entity
     * @return self
     */
    public static function fromEntity(RoomEntity $entity): self
    {
        return new self(
            id: $entity->id,
            name: $entity->name,
            depth: $entity->depth,
            children: array_map(
                fn(RoomEntity $child) => self::fromEntity($child),
                $entity->children
            ),
        );
    }

    /**
     * @param RoomEntity[] $entities
     * @return self[]
     */
    public static function fromEntityArray(array $entities): array
    {
        return array_map(fn(RoomEntity $entity) => self::fromEntity($entity), $entities);
    }
}
