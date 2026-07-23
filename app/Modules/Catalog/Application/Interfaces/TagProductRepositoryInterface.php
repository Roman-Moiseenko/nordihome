<?php

namespace App\Modules\Catalog\Application\Interfaces;

interface TagProductRepositoryInterface
{
    /**
     * Возвращает ассоциативный массив [tag_id => count] для переданных ID тегов.
     * @param int[] $tagIds
     * @return array<int, int>
     */
    public function countProductsByTagIds(array $tagIds): array;
    public function countProductsByTagId(int $tagId): int;
}
