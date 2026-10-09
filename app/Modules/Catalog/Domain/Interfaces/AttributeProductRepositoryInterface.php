<?php

namespace App\Modules\Catalog\Domain\Interfaces;

interface AttributeProductRepositoryInterface
{
    /**
     * Возвращает значение атрибута у товара.
     * Для variant — массив id, для скаляров — само значение.
     */
    public function valueOf(int $productId, int $attributeId): mixed;

    /**
     * Атрибуты товара вместе со значениями и вариантами.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getForProduct(int $productId): array;

    /**
     * Атрибуты категории товара, которые ещё не назначены товару.
     *
     * @return array<int, array{id: int, name: string}>
     */
    public function getPossibleForProduct(int $productId): array;

    /**
     * Полная пересинхронизация атрибутов товара (по логике legacy editAttribute).
     *
     * @param array<int, array{id: int, value?: mixed}> $attributes
     */
    public function syncProductAttributes(int $productId, array $attributes): void;
}
