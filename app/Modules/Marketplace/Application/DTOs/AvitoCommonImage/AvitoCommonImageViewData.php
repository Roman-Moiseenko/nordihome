<?php

namespace App\Modules\Marketplace\Application\DTOs\AvitoCommonImage;

use App\Modules\Marketplace\Domain\Entities\AvitoCommonImageEntity;
use Spatie\LaravelData\Data;

class AvitoCommonImageViewData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $name,
        public readonly bool $active,
        public readonly int $sort,
        public readonly ?string $image,
    ) {}

    public static function fromEntity(AvitoCommonImageEntity $entity): self
    {
        return new self(
            id: $entity->id,
            name: $entity->name,
            active: $entity->active,
            sort: $entity->sort,
            image: $entity->image?->getUrl(),
        );
    }
}
