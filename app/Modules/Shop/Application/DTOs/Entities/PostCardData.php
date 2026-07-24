<?php

namespace App\Modules\Shop\Application\DTOs\Entities;

class PostCardData
{

    public function __construct(
        public int $id,
        public string $slug,
        public string $caption,
        public string $fragment,
        public string $image,
    )
    {
    }
}
