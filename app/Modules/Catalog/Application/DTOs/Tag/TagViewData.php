<?php

namespace App\Modules\Catalog\Application\DTOs\Tag;

use App\Modules\Catalog\Domain\Entities\TagEntity;

class TagViewData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public string $image,
        public bool $isMain,
      //  public int $count
    )
    {

    }

    public static function fromEntity(TagEntity $tagEntity): self
    {
        return new self(
            id: $tagEntity->id,
            name: $tagEntity->name,
            slug: $tagEntity->slug,
            image: $tagEntity->image_url,
            isMain: $tagEntity->isMain,
        );
    }
}
