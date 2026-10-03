<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\PricingRuleCategory;

use App\Modules\Accounting\Domain\Interfaces\PricingRuleCategoryRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use DomainException;

readonly class SyncPricingRuleCategoriesUseCase
{
    public function __construct(
        private PricingRuleCategoryRepositoryInterface $pricingRuleCategoryRepository,
    ) {
    }

    /**
     * @param int[] $categoryIds
     */
    public function execute(int $pricingRuleId, array $categoryIds, UserPermission $userPermission): void
    {
        if (!$userPermission->can('accounting.pricing.edit')) {
            throw new AccessDeniedException();
        }

        foreach ($categoryIds as $categoryId) {
            if ($this->pricingRuleCategoryRepository->existsCategoryInOtherRule($categoryId, $pricingRuleId)) {
                throw new DomainException('Категория уже привязана к другому правилу расчета цены');
            }
        }

        $this->pricingRuleCategoryRepository->syncCategories($pricingRuleId, $categoryIds);
    }
}
