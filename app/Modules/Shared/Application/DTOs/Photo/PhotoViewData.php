<?php

namespace App\Modules\Shared\Application\DTOs\Photo;

use App\Modules\Shared\Domain\Entities\PhotoEntity;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use Spatie\LaravelData\Data;

class PhotoViewData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $url,
        public readonly string $thumb,
        public readonly string $alt,
        public readonly string $title,
        public readonly string $description,
    )
    {
    }

    public static function fromEntity(PhotoEntity $entity): self
    {
        $thumb = '';
        if ($entity->id !== null && $entity->file !== '') {
            $thumb = app(PhotoService::class)->getThumbUrl(
                $entity->id,
                $entity->modelType,
                $entity->imageableId,
                $entity->file,
                'thumb',
            );
        }

        return new self(
            $entity->id,
            $entity->uploadUrl,
            $thumb,
            $entity->alt,
            $entity->title,
            $entity->description
        );
    }
}
