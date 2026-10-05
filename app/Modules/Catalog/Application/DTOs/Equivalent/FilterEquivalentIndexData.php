<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Equivalent;

use Spatie\LaravelData\Data;

class FilterEquivalentIndexData extends Data
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $product = null,
        public readonly ?int $category = null,
        public readonly int $perPage = 20,
        public ?int $count = 0,
    ) {}
}
