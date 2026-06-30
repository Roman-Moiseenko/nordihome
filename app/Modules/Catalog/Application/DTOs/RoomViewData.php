<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs;

use App\Modules\Catalog\Domain\Entities\RoomEntity;
use Spatie\LaravelData\Data;

class RoomViewData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $svgIcon,
        public readonly bool $published,
        public readonly ?array $meta,
        public readonly ?int $parentId,
        public readonly int $left,
        public readonly int $right,
        public readonly int $depth,
        /** @var RoomViewData[] */
        public readonly array $children = [],
    )
    {
    }

    public static function fromEntity(RoomEntity $room): self
    {
        return new self(
            id: $room->id,
            name: $room->name,
            slug: (string) $room->slug,
            svgIcon: $room->svgIcon,
            published: $room->isPublished(),
            meta: $room->meta ? [
                'title' => $room->meta->getTitle(),
                'description' => $room->meta->getDescription(),
            ] : null,
            parentId: $room->parentId,
            left: $room->left,
            right: $room->right,
            depth: $room->depth,
            children: array_map(
                fn(RoomEntity $child) => self::fromEntity($child),
                $room->children
            ),
        );
    }
}
