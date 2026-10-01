<?php

namespace App\Modules\Accounting\Application\DTOs\Exchange;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class PriceOutAckPayloadData extends Data
{
    public function __construct(
        #[Required, In('1c.price-ack')]
        public string $event,

        #[Required, BooleanType]
        public bool $succeed,
    ) {}
}
