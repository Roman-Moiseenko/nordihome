<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Interfaces;

interface PricingRuleCategoryRepositoryInterface
{
    /**
     * ID категорий, привязанных к правилу.
     *
     * @return int[]
     */
    public function getCategoryIdsByRuleId(int $pricingRuleId): array;

    /**
     * Заменить весь набор категорий правила.
     *
     * @param int[] $categoryIds
     */
    public function syncCategories(int $pricingRuleId, array $categoryIds): void;

    public function existsCategoryInOtherRule(int $categoryId, int $excludePricingRuleId): bool;
}
