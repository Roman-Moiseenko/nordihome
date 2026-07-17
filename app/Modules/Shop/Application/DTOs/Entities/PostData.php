<?php

namespace App\Modules\Shop\Application\DTOs\Entities;

class PostData
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
