<?php

namespace App\Modules\Catalog\Application\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TagProductRepositoryInterface
{
    /**
     * Возвращает ассоциативный массив [tag_id => count] для переданных ID тегов.
     * @param int[] $tagIds
     * @return array<int, int>
     */
    public function countProductsByTagIds(array $tagIds): array;
    public function countProductsByTagId(int $tagId): int;

    /** @return int[] */
    public function getTagsByProductId(int $productId): array;

    public function syncProducts(int $tagId, array $productIds): void;
    public function syncTags(int $productId, array $tagIds): void;
    public function attachProducts(int $tagId, array $productIds): void;
    public function attachTags(int $productId, array $tagIds): void;
    public function detachProducts(int $tagId, array $productIds): void;
    public function detachTags(int $productId, array $tagIds): void;

    public function getProductIdsByTag(int $tagId, int $perPage = 15): LengthAwarePaginator;
}
