<?php

namespace App\Modules\Order\Application\DTOs\Order;

use Spatie\LaravelData\Data;

class FilterOrderIndexData  extends Data
{
    public function __construct(
        public readonly ?string $dateFrom = null,
        public readonly ?string $dateTo = null,
        public readonly ?string $client = null,
        public readonly ?string $comment = null,
        public readonly ?int    $staffId = null,
        public readonly ?string $status = null,
        public readonly int     $perPage = 20,
        public ?int    $count = 0,
    )
    {
    }
}
