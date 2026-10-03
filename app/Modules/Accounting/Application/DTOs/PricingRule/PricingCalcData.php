<?php

namespace App\Modules\Accounting\Application\DTOs\PricingRule;

use Spatie\LaravelData\Data;

class PricingCalcData extends Data
{
    public function __construct(
        public readonly int $retail,
        public readonly int $bulk,
    ) {}
}
