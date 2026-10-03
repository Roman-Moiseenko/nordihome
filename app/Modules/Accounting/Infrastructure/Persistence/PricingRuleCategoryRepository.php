<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Persistence;

use App\Modules\Accounting\Domain\Interfaces\PricingRuleCategoryRepositoryInterface;
use App\Modules\Accounting\Infrastructure\Models\PricingRuleCategory;

class PricingRuleCategoryRepository implements PricingRuleCategoryRepositoryInterface
{
    public function getCategoryIdsByRuleId(int $pricingRuleId): array
    {
        return PricingRuleCategory::query()
            ->where('pricing_rule_id', $pricingRuleId)
            ->pluck('category_id')
            ->map(fn($id) => (int) $id)
            ->all();
    }

    public function syncCategories(int $pricingRuleId, array $categoryIds): void
    {
        PricingRuleCategory::query()
            ->where('pricing_rule_id', $pricingRuleId)
            ->delete();

        foreach (array_unique($categoryIds) as $categoryId) {
            $pivot = new PricingRuleCategory();
            $pivot->pricing_rule_id = $pricingRuleId;
            $pivot->category_id = (int) $categoryId;
            $pivot->save();
        }
    }

    public function existsCategoryInOtherRule(int $categoryId, int $excludePricingRuleId): bool
    {
        return PricingRuleCategory::query()
            ->where('category_id', $categoryId)
            ->where('pricing_rule_id', '!=', $excludePricingRuleId)
            ->exists();
    }
}
