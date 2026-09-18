<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\Stock;

use Spatie\LaravelData\Data;

class FilterStockIndexData extends Data
{
    public function __construct(
        public readonly ?string $code = null,
        public readonly ?int $categoryId = null,
        public readonly int $perPage = 20,
        public ?int $count = 0,
    ) {
    }
}
