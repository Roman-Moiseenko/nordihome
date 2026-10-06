<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Interfaces;

interface AttributeCategoryRepositoryInterface
{
    /**
     * Получить ID категорий, привязанных к атрибуту.
     *
     * @return int[]
     */
    public function getCategoryIdsByAttributeId(int $attributeId): array;

    /**
     * Получить ID категорий для нескольких атрибутов (без N+1 для списка).
     *
     * @param int[] $attributeIds
     * @return array<int, int[]>
     */
    public function getCategoryIdsByAttributeIds(array $attributeIds): array;

    /**
     * Привязать категории к атрибуту (дополняет существующие).
     *
     * @param int[] $categoryIds
     */
    public function attachCategories(int $attributeId, array $categoryIds): void;

    /**
     * Синхронизировать категории атрибута (заменяет весь набор).
     *
     * @param int[] $categoryIds
     */
    public function syncCategories(int $attributeId, array $categoryIds): void;

    /**
     * Отвязать категории от атрибута.
     *
     * @param int[] $categoryIds
     */
    public function detachCategories(int $attributeId, array $categoryIds): void;
}
