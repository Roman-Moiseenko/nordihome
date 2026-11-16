<?php

namespace App\Modules\Marketplace\Domain\Interfaces;

use App\Modules\Marketplace\Domain\Entities\AvitoProductValueEntity;

interface AvitoProductValueRepositoryInterface
{
    /**
     * Значения параметров категории для объявления Авито.
     *
     * @return AvitoProductValueEntity[]
     */
    public function getByProductId(int $avitoProductId): array;

    public function save(AvitoProductValueEntity $value): AvitoProductValueEntity;

    /**
     * Удалить все значения параметров объявления (при смене категории).
     */
    public function deleteByProductId(int $avitoProductId): void;
}
