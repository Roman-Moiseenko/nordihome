<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\PricingRule;

use App\Modules\Accounting\Application\DTOs\PricingRule\PricingRuleCreateData;
use App\Modules\Accounting\Domain\Entities\PricingRuleEntity;
use App\Modules\Accounting\Domain\Interfaces\PricingRuleRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class CreatePricingRuleUseCase
{
    public function __construct(
        private PricingRuleRepositoryInterface $pricingRuleRepository,
    ) {
    }

    public function execute(PricingRuleCreateData $dto, UserPermission $userPermission): PricingRuleEntity
    {
        if (!$userPermission->can('accounting.pricing.create')) {
            throw new AccessDeniedException();
        }

        $rule = new PricingRuleEntity(
            name: $dto->name,
            ratioWeight: $dto->ratioWeight,
            ratioMarkup: $dto->ratioMarkup,
            roundingStep: $dto->roundingStep ?? 100,
            roundingSubtract: $dto->roundingSubtract ?? 10,
            isActive: $dto->isActive ?? true,
        );

        return $this->pricingRuleRepository->save($rule);
    }
}
