<?php

namespace App\Modules\Catalog\Application\DTOs\Tag;

class TagCreateData
{
    public function __construct(
        public string $name,
        public string $slug,
    )
    {}
}
