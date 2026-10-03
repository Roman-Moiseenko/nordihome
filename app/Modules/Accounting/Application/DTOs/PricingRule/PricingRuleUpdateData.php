<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\PricingRule;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class PricingRuleUpdateData extends Data
{
    public function __construct(
        #[Nullable, StringType, Max(100)]
        public readonly ?string $name,
        #[Nullable, Numeric]
        public readonly ?float $ratioWeight,
        #[Nullable, Numeric]
        public readonly ?float $ratioMarkup,
        #[Nullable, Numeric]
        public readonly ?int $roundingStep,
        #[Nullable, Numeric]
        public readonly ?int $roundingSubtract,
        #[Nullable, BooleanType]
        public readonly ?bool $isActive,
    ) {
    }
}
