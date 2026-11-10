<?php

namespace App\Modules\Marketplace\Domain\Interfaces;

interface AvitoCategoryFieldRepositoryInterface
{
    /**
     * Количество параметров для каждой категории.
     *
     * @param int[] $categoryIds
     * @return array<int, int> map avito_category_id => count
     */
    public function countFieldsByCategoryIds(array $categoryIds): array;

    /**
     * Заменяет набор параметров категории данными из Авито.
     *
     * @param array<int, array{tag: string, name: string, type: string, is_required: bool, allowed_values: ?array, depends_on: ?array}> $fields
     */
    public function syncForCategory(int $avitoCategoryId, array $fields): void;
}
