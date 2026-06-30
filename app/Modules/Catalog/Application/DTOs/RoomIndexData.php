<?php

namespace App\Modules\Catalog\Application\DTOs;

use App\Modules\Catalog\Domain\Entities\RoomEntity;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class RoomIndexData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $image,
        public readonly ?string $icon,
        /** @var RoomIndexData[] */
        public readonly array $children = []
    )
    {
    }

    public static function fromEntity(RoomEntity $room): self
    {
        return new self(
            id: $room->id,
            name: $room->name,
            slug: (string) $room->slug,
            image: $room->image?->getUrl(),
            icon: $room->icon?->getUrl(),
            children: array_map(
                fn(RoomEntity $child) => self::fromEntity($child),
                $room->children
            ),
        );
    }
}
