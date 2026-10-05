<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Brand;

use Spatie\LaravelData\Data;

class FilterBrandIndexData extends Data
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly int $perPage = 20,
        public ?int $count = 0,
    ) {}
}
