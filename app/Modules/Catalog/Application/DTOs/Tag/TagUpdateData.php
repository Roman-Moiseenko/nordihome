<?php

namespace App\Modules\Catalog\Application\DTOs\Tag;

class TagUpdateData
{
    public function __construct(
        public string $name,
        public string $slug,
        public bool $isMain,
    )
    {

    }
}
