<?php

namespace App\Modules\Content\Domain\Interfaces;

interface LabelPostRepositoryInterface
{
    /**
     * Возвращает ассоциативный массив [label_id => count] для переданных ID меток.
     *
     * @param int[] $labelIds
     * @return array<int, int>
     */
    public function countPostsByLabelIds(array $labelIds): array;

    public function countPostsByLabelId(int $labelId): int;

    /** @return int[] */
    public function getLabelsByPostId(int $postId): array;

    /**
     * Заменяет весь набор меток у записи. Пустой массив очищает метки.
     *
     * @param int[] $labelIds
     */
    public function syncLabels(int $postId, array $labelIds): void;
}
