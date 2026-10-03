<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Interfaces;

use App\Modules\Accounting\Domain\Entities\PricingRuleEntity;

interface PricingRuleRepositoryInterface
{
    /** @return PricingRuleEntity[] */
    public function getAll(): array;

    public function getById(int $id): PricingRuleEntity;

    public function findByCategoryId(int $categoryId): ?PricingRuleEntity;

    public function save(PricingRuleEntity $rule): PricingRuleEntity;

    public function delete(int $id): void;
}
