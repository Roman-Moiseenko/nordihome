<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\PricingRule;

use App\Modules\Accounting\Domain\Entities\PricingRuleEntity;
use Spatie\LaravelData\Data;

class PricingRuleIndexData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public bool $isActive,
        public int $categoriesCount,
    ) {
    }

    public static function fromEntity(PricingRuleEntity $rule): self
    {
        return new self(
            id: $rule->id,
            name: $rule->name,
            isActive: $rule->isActive(),
            categoriesCount: $rule->categoriesCount,
        );
    }
}
