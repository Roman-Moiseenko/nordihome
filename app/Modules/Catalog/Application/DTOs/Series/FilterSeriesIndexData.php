<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Series;

use Spatie\LaravelData\Data;

class FilterSeriesIndexData extends Data
{
    public function __construct(
        public readonly ?string $product = null,
        public readonly ?string $series = null,
        public readonly int $perPage = 20,
        public ?int $count = 0,
    ) {}
}
