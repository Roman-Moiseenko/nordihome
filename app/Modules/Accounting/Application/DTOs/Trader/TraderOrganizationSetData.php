<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\Trader;

use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class TraderOrganizationSetData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $organization,
    ) {
    }
}
