<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Group;

use App\Modules\Catalog\Domain\Entities\GroupEntity;
use Spatie\LaravelData\Data;

/**
 * DTO для страницы просмотра группы (Catalog/Group/Show).
 * Товары группы подгружаются отдельно через TableRelation.
 */
class GroupViewData extends Data
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly string  $slug,
        public readonly string  $description,
        public readonly bool    $published,
    )
    {
    }

    public static function fromEntity(GroupEntity $group): self
    {
        return new self(
            id: $group->id,
            name: $group->name,
            slug: (string) $group->slug,
            description: $group->description,
            published: $group->isPublished(),
        );
    }
}
