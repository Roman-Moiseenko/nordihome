<?php

namespace App\Modules\Marketplace\Domain\Interfaces;

use App\Modules\Marketplace\Domain\Entities\AvitoCommonImageEntity;

interface AvitoCommonImageRepositoryInterface
{
    /** @return AvitoCommonImageEntity[] */
    public function getAll(): array;

    public function getById(int $id): AvitoCommonImageEntity;

    public function save(AvitoCommonImageEntity $entity): AvitoCommonImageEntity;

    public function delete(int $id): void;

    /**
     * Перестраивает глобальный порядок сортировки по массиву id.
     *
     * @param int[] $ids
     */
    public function reorder(array $ids): void;

    /**
     * Следующее значение sort для новой записи.
     */
    public function nextSort(): int;
}
