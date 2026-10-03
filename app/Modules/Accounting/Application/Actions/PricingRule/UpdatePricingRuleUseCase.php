<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\PricingRule;

use App\Modules\Accounting\Application\DTOs\PricingRule\PricingRuleUpdateData;
use App\Modules\Accounting\Domain\Entities\PricingRuleEntity;
use App\Modules\Accounting\Domain\Interfaces\PricingRuleRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdatePricingRuleUseCase
{
    public function __construct(
        private PricingRuleRepositoryInterface $pricingRuleRepository,
    ) {
    }

    public function execute(int $id, PricingRuleUpdateData $dto, UserPermission $userPermission): PricingRuleEntity
    {
        if (!$userPermission->can('accounting.pricing.edit')) {
            throw new AccessDeniedException();
        }

        $rule = $this->pricingRuleRepository->getById($id);

        if ($dto->name !== null) {
            $rule->name = $dto->name;
        }

        if ($dto->ratioWeight !== null) {
            $rule->ratioWeight = $dto->ratioWeight;
        }

        if ($dto->ratioMarkup !== null) {
            $rule->ratioMarkup = $dto->ratioMarkup;
        }

        if ($dto->roundingStep !== null) {
            $rule->roundingStep = $dto->roundingStep;
        }

        if ($dto->roundingSubtract !== null) {
            $rule->roundingSubtract = $dto->roundingSubtract;
        }

        if ($dto->isActive !== null) {
            $dto->isActive ? $rule->activate() : $rule->deactivate();
        }

        return $this->pricingRuleRepository->save($rule);
    }
}
