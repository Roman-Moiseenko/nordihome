<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\DTOs;

use Spatie\LaravelData\Data;

class IkeaTreeClientData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $image,
        /** @var self[] */
        public readonly array $children = [],
    )
    {
    }
}
