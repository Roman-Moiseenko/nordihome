<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Interfaces;

use App\Modules\Catalog\Application\DTOs\Modification\ModificationIndexData;
use App\Modules\Catalog\Application\DTOs\Modification\ModificationViewData;
use App\Modules\Catalog\Domain\Entities\ModificationEntity;
use Illuminate\Pagination\LengthAwarePaginator;

interface ModificationRepositoryInterface
{
    public function getById(int $id): ModificationEntity;

    /**
     * Данные карточки модификации: название, оси с вариантами
     * и товары (из pivot + products).
     */
    public function getViewData(int $id): ModificationViewData;

    /**
     * @return LengthAwarePaginator<ModificationIndexData>
     */
    public function findAll(int $perPage = 20, int $page = 1): LengthAwarePaginator;

    /**
     * Сохраняет агрегат целиком: строку modifications,
     * оси (modification_attributes) и связи товаров
     * (modifications_products + modification_product_values).
     */
    public function save(ModificationEntity $modification): ModificationEntity;

    public function delete(int $id): void;

    /**
     * ID товаров, уже участвующих в любой модификации.
     *
     * @return int[]
     */
    public function getUsedProductIds(): array;
}
