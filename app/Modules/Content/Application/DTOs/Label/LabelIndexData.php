<?php

namespace App\Modules\Content\Application\DTOs\Label;

use App\Modules\Content\Domain\Entities\LabelEntity;

class LabelIndexData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public int $count
    )
    {

    }

    public static function fromEntity(LabelEntity $label, int $count): LabelIndexData
    {
        return new self(
            $label->id,
            $label->name,
            $label->slug,
            $count
        );
    }
}
