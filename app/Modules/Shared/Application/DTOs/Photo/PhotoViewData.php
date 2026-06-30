<?php

namespace App\Modules\Shared\Application\DTOs\Photo;

use App\Modules\Shared\Domain\Entities\PhotoEntity;
use Spatie\LaravelData\Data;

class PhotoViewData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $url,
        public readonly string $alt,
        public readonly string $title,
        public readonly string $description,
    )
    {
    }

    public static function fromEntity(PhotoEntity $entity): self
    {
        return new self(
            $entity->id,
            $entity->uploadUrl,
            $entity->alt,
            $entity->title,
            $entity->description
        );
    }
}
