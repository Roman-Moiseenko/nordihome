<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\PricingRule;

use App\Modules\Accounting\Domain\Entities\PricingRuleEntity;
use Spatie\LaravelData\Data;

class PricingRuleViewData extends Data
{
    /**
     * @param int[] $categoryIds
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly float $ratioWeight,
        public readonly float $ratioMarkup,
        public readonly int $roundingStep,
        public readonly int $roundingSubtract,
        public readonly bool $isActive,
        public readonly array $categoryIds = [],
    ) {
    }

    /**
     * @param int[] $categoryIds
     */
    public static function fromEntity(PricingRuleEntity $rule, array $categoryIds = []): self
    {
        return new self(
            id: $rule->id,
            name: $rule->name,
            ratioWeight: $rule->ratioWeight,
            ratioMarkup: $rule->ratioMarkup,
            roundingStep: $rule->roundingStep,
            roundingSubtract: $rule->roundingSubtract,
            isActive: $rule->isActive(),
            categoryIds: $categoryIds,
        );
    }
}
