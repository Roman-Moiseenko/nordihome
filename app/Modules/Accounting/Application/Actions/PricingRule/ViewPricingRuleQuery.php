<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\PricingRule;

use App\Modules\Accounting\Application\DTOs\PricingRule\PricingRuleViewData;
use App\Modules\Accounting\Domain\Interfaces\PricingRuleCategoryRepositoryInterface;
use App\Modules\Accounting\Domain\Interfaces\PricingRuleRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ViewPricingRuleQuery
{
    public function __construct(
        private PricingRuleRepositoryInterface $pricingRuleRepository,
        private PricingRuleCategoryRepositoryInterface $pricingRuleCategoryRepository,
    ) {
    }

    public function execute(int $id, UserPermission $userPermission): PricingRuleViewData
    {
        if (!$userPermission->can('accounting.pricing.view')) {
            throw new AccessDeniedException();
        }

        $rule = $this->pricingRuleRepository->getById($id);
        $categoryIds = $this->pricingRuleCategoryRepository->getCategoryIdsByRuleId($id);

        return PricingRuleViewData::fromEntity($rule, $categoryIds);
    }
}
