<?php

namespace App\Modules\Catalog\Application\DTOs\Room;

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
        public readonly int $depth,
        public readonly bool $published,
        public readonly ?string $image_url,
        public readonly ?string $icon_url,
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
            depth: $room->depth,
            published: $room->isPublished(),
            image_url: $room->image_url,
            icon_url: $room->icon_url,
            children: array_map(
                fn(RoomEntity $child) => self::fromEntity($child),
                $room->children
            ),
        );
    }
}
