<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Group;

use App\Modules\Catalog\Domain\Entities\GroupEntity;

class GroupIndexData
{
    public function __construct(
        public int $id,
        public string $name,
        public int $quantity,
        public bool $published,
        public string $description,
    ) {}

    public static function fromEntity(GroupEntity $group): self
    {
        return new self(
            id: $group->id,
            name: $group->name,
            quantity: $group->quantity,
            published: $group->isPublished(),
            description: $group->description,
        );
    }
}
