<?php

namespace App\Modules\Shop\Application\DTOs\Entities;

readonly class PostCategoryData
{

    public function __construct(
        public int $id,
        public string $slug,
        public string $title,
        public string $description,
    )
    {
    }
}
