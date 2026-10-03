<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\PricingRule;

use App\Modules\Accounting\Domain\Interfaces\PricingRuleRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class RemovePricingRuleUseCase
{
    public function __construct(
        private PricingRuleRepositoryInterface $pricingRuleRepository,
    ) {
    }

    public function execute(int $id, UserPermission $userPermission): void
    {
        if (!$userPermission->can('accounting.pricing.delete')) {
            throw new AccessDeniedException();
        }

        $this->pricingRuleRepository->delete($id);
    }
}
