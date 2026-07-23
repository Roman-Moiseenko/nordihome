<?php

namespace App\Modules\Catalog\Application\DTOs\Tag;

use App\Modules\Catalog\Domain\Entities\TagEntity;

class TagIndexData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public bool $isMain,
        public int $count
    )
    {

    }

    public static function fromEntity(TagEntity $tag, int $count): TagIndexData
    {
        return new self(
            $tag->id,
            $tag->name,
            $tag->slug,
            $tag->isMain,
            $count
        );
    }
}
