<?php

namespace App\Modules\Lead\Application\DTOs\Lead;

use Spatie\LaravelData\Data;

class LeadOrderItemData extends Data
{
    public function __construct(
        public string $code,
        public int $quantity,
    )
    {
    }
}
