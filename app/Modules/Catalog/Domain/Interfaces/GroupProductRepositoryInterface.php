<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;

interface GroupProductRepositoryInterface
{
    /**
     * Получить ID товаров, привязанных к группе (с пагинацией).
     *
     * @param int $groupId
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function getProductIdsByGroupId(int $groupId, int $perPage = 15, int $page = 1): LengthAwarePaginator;

    /**
     * Синхронизировать товары группы (заменить весь набор).
     *
     * @param int   $groupId
     * @param int[] $productIds
     */
    public function syncProducts(int $groupId, array $productIds): void;

    /**
     * Привязать товары к группе (добавление к существующим).
     *
     * @param int   $groupId
     * @param int[] $productIds
     */
    public function attachProducts(int $groupId, array $productIds): void;

    /**
     * Отвязать товары от группы.
     *
     * @param int   $groupId
     * @param int[] $productIds
     */
    public function detachProducts(int $groupId, array $productIds): void;

    /**
     * Отвязать все товары от группы.
     *
     * @param int $groupId
     */
    public function detachAllProducts(int $groupId): void;
    /**
     * Возвращает ассоциативный массив [group_id => count] для переданных ID групп.
     * @param int[] $groupIds
     * @return array<int, int>
     */
    public function countProductsByGroupIds(array $groupIds): array;
}
