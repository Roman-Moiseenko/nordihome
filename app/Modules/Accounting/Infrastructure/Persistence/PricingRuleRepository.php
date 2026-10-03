<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Persistence;

use App\Modules\Accounting\Domain\Entities\PricingRuleEntity;
use App\Modules\Accounting\Domain\Interfaces\PricingRuleRepositoryInterface;
use App\Modules\Accounting\Infrastructure\Models\PricingRule;
use DateTimeImmutable;

class PricingRuleRepository implements PricingRuleRepositoryInterface
{
    public function getAll(): array
    {
        return PricingRule::query()
            ->withCount('categories')
            ->orderBy('name')
            ->get()
            ->map(fn(PricingRule $model) => $this->hydrate($model))
            ->all();
    }

    public function getById(int $id): PricingRuleEntity
    {
        return $this->hydrate(PricingRule::query()->findOrFail($id));
    }

    public function findByCategoryId(int $categoryId): ?PricingRuleEntity
    {
        $model = PricingRule::query()
            ->whereHas('categories', fn($query) => $query->where('category_id', $categoryId))
            ->first();

        return $model ? $this->hydrate($model) : null;
    }

    public function save(PricingRuleEntity $rule): PricingRuleEntity
    {
        $model = $rule->id
            ? PricingRule::query()->findOrFail($rule->id)
            : new PricingRule();

        $model->name = $rule->name;
        $model->ratio_weight = $rule->ratioWeight;
        $model->ratio_markup = $rule->ratioMarkup;
        $model->rounding_step = $rule->roundingStep;
        $model->rounding_subtract = $rule->roundingSubtract;
        $model->is_active = $rule->isActive();

        $model->save();

        return $this->hydrate($model->fresh());
    }

    public function delete(int $id): void
    {
        $model = PricingRule::query()->findOrFail($id);
        $model->delete();
    }

    private function hydrate(PricingRule $model): PricingRuleEntity
    {
        $rule = new PricingRuleEntity(
            name: $model->name,
            ratioWeight: (float) $model->ratio_weight,
            ratioMarkup: (float) $model->ratio_markup,
            roundingStep: (int) $model->rounding_step,
            roundingSubtract: (int) $model->rounding_subtract,
            isActive: (bool) $model->is_active,
        );

        $rule->id = $model->id;

        if ($model->created_at) {
            $rule->createdAt = DateTimeImmutable::createFromMutable($model->created_at);
        }

        if ($model->updated_at) {
            $rule->updatedAt = DateTimeImmutable::createFromMutable($model->updated_at);
        }

        if (array_key_exists('categories_count', $model->getAttributes())) {
            $rule->categoriesCount = (int) $model->categories_count;
        }

        return $rule;
    }
}
