<?php

namespace App\Modules\Accounting\Application\DTOs\Exchange;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class PriceOutPayloadData extends Data
{
    public function __construct(
        #[Required, In('1c.price-out')]
        public string $event,
    ) {}
}
