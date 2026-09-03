<?php

namespace App\Modules\Order\Application\DTOs\Order;

use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Data;
use Symfony\Contracts\Service\Attribute\Required;

class AssignClientToOrderData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public int $orderId,
        #[Required, Numeric]
        public int $clientId,
    ) {}
}
