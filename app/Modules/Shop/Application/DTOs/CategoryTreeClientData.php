<?php

namespace App\Modules\Shop\Application\DTOs;

use Spatie\LaravelData\Data;

class CategoryTreeClientData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $svg,
        public readonly string $image,
        /** @var self[] */
        public readonly array $children = [],
    )
    {
    }
}
