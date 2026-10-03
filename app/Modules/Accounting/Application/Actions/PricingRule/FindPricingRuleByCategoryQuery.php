<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\PricingRule;

use App\Modules\Accounting\Domain\Entities\PricingRuleEntity;
use App\Modules\Accounting\Domain\Interfaces\PricingRuleRepositoryInterface;

readonly class FindPricingRuleByCategoryQuery
{
    public function __construct(
        private PricingRuleRepositoryInterface $pricingRuleRepository,
    ) {
    }

    public function execute(int $categoryId): ?PricingRuleEntity
    {
        return $this->pricingRuleRepository->findByCategoryId($categoryId);
    }
}
