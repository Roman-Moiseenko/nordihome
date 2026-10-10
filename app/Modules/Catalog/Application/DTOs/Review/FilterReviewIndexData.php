<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Review;

use Spatie\LaravelData\Data;

class FilterReviewIndexData extends Data
{
    public function __construct(
        public readonly ?string $status = null,
        public readonly ?string $dateFrom = null,
        public readonly ?string $dateTo = null,
        public readonly int $perPage = 20,
        public ?int $count = 0,
    ) {}
}
