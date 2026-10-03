<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\PricingRule;

use App\Modules\Accounting\Domain\Entities\PricingRuleEntity;
use App\Modules\Accounting\Domain\Interfaces\PricingRuleRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class IndexPricingRuleQuery
{
    public function __construct(
        private PricingRuleRepositoryInterface $pricingRuleRepository,
    ) {
    }

    /** @return PricingRuleEntity[] */
    public function execute(UserPermission $userPermission): array
    {
        if (!$userPermission->can('accounting.pricing.view')) {
            throw new AccessDeniedException();
        }

        return $this->pricingRuleRepository->getAll();
    }
}
