<?php

namespace App\Modules\Content\Application\DTOs\Label;

use App\Modules\Content\Domain\Entities\LabelEntity;

class LabelViewData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
    )
    {

    }

    public static function fromEntity(LabelEntity $labelEntity): self
    {
        return new self(
            id: $labelEntity->id,
            name: $labelEntity->name,
            slug: $labelEntity->slug,
        );
    }
}
