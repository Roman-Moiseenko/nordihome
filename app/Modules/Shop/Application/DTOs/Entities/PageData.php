<?php

namespace App\Modules\Shop\Application\DTOs\Entities;

use App\Modules\Shop\Application\DTOs\Elements\ImageInfoData;

class PageData
{
    public function __construct(
        public int $id,
        public string $slug,
        public string $name,
        public string $title,
        public string $description,
        public string $fragment,
        public string $text,
        public string $template,
        public bool $oldRender,
        public ImageInfoData $image,
        public string $publishedAt,
        public string $updatedAt,
    )
    {
    }
}
