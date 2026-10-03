<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\PricingRule;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class PricingRuleCreateData extends Data
{
    public function __construct(
        #[Required, StringType, Max(100)]
        public readonly string $name,
        #[Required, Numeric]
        public readonly float $ratioWeight,
        #[Required, Numeric]
        public readonly float $ratioMarkup,
        #[Nullable, Numeric]
        public readonly ?int $roundingStep = 100,
        #[Nullable, Numeric]
        public readonly ?int $roundingSubtract = 10,
        #[Nullable, BooleanType]
        public readonly ?bool $isActive = true,
    ) {
    }
}
