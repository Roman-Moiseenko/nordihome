<?php

namespace App\Modules\Shop\Application\DTOs\Entities;

use App\Modules\Shop\Application\DTOs\Elements\ImageInfoData;

class PostCardData
{

    public function __construct(
        public int $id,
        public string $slug,
        public string $caption,
        public string $fragment,
        public ImageInfoData $image,
    )
    {
    }
}
