<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;

interface EquivalentProductRepositoryInterface
{
    /**
     * Получить ID товаров, привязанных к группе аналогов (с пагинацией).
     */
    public function getProductIdsByEquivalentId(int $equivalentId, int $perPage = 15, int $page = 1): LengthAwarePaginator;

    /**
     * Получить ID групп аналогов, привязанных к товару.
     *
     * @return int[]
     */
    public function getEquivalentIdsByProductId(int $productId): array;

    /**
     * Привязать товары к группе аналогов (дополняет существующие, без дублей).
     *
     * @param int[] $productIds
     */
    public function attachProducts(int $equivalentId, array $productIds): void;

    /**
     * Заменить весь набор товаров группы аналогов.
     *
     * @param int[] $productIds
     */
    public function syncProducts(int $equivalentId, array $productIds): void;

    /**
     * Отвязать товары от группы аналогов.
     *
     * @param int[] $productIds
     */
    public function detachProducts(int $equivalentId, array $productIds): void;

    /**
     * Отвязать все товары от группы аналогов.
     */
    public function detachAllProducts(int $equivalentId): void;

    /**
     * Привязать группы аналогов к товару.
     *
     * @param int[] $equivalentIds
     */
    public function attachEquivalents(int $productId, array $equivalentIds): void;

    /**
     * Заменить весь набор групп аналогов товара.
     *
     * @param int[] $equivalentIds
     */
    public function syncEquivalents(int $productId, array $equivalentIds): void;

    /**
     * Отвязать группы аналогов от товара.
     *
     * @param int[] $equivalentIds
     */
    public function detachEquivalents(int $productId, array $equivalentIds): void;

    /**
     * Возвращает ассоциативный массив [equivalent_id => count] для переданных ID групп аналогов.
     *
     * @param int[] $equivalentIds
     * @return array<int, int>
     */
    public function countProductsByEquivalentIds(array $equivalentIds): array;
}
