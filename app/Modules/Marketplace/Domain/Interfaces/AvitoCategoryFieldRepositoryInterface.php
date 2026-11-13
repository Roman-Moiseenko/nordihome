<?php

namespace App\Modules\Marketplace\Domain\Interfaces;

use App\Modules\Marketplace\Domain\Entities\AvitoCategoryFieldEntity;

interface AvitoCategoryFieldRepositoryInterface
{
    /**
     * Количество параметров для каждой категории.
     *
     * @param int[] $categoryIds
     * @return array<int, int> map avito_category_id => count
     */
    public function countFieldsByCategoryIds(array $categoryIds): array;

    public function getById(int $id): AvitoCategoryFieldEntity;

    public function save(AvitoCategoryFieldEntity $field): AvitoCategoryFieldEntity;

    /**
     * Заменяет набор параметров категории данными из Авито.
     *
     * @param array<int, array{tag: string, name: string, type: ?string, is_required: bool, allowed_values: ?array, depends_on: ?array}> $fields
     */
    public function syncForCategory(int $avitoCategoryId, array $fields): void;

    /**
     * Добавляет/обновляет параметры категории, дополняя варианты без дублей
     * (импорт структуры из xlsx).
     *
     * @param array<int, array{tag: string, name: string, type: ?string, is_required: bool, allowed_values: ?array, depends_on: ?array}> $fields
     */
    public function upsertForCategory(int $avitoCategoryId, array $fields): void;

    /**
     * @return array<int, array{id: int, tag: string, name: string, type: ?string, is_required: bool, allowed_values: ?array}>
     */
    public function getByCategoryId(int $avitoCategoryId): array;
}
