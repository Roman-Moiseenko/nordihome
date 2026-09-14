<?php

namespace App\Modules\Shop\Application\DTOs\Entities;

use App\Modules\Shop\Application\DTOs\Elements\ImageInfoData;

class PostData
{
    public function __construct(
        public int $id,
        public string $slug,
        public string $title,
        public string $description,
        public string $caption,
        public string $fragment,
        public ImageInfoData $image,
        public string $publishedAt,
        public string $updatedAt,
    )
    {
    }
}
