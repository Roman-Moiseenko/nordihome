<?php

namespace App\Modules\Marketplace\Domain\Interfaces;

use App\Modules\Marketplace\Application\DTOs\AvitoProduct\FilterAvitoProductIndexData;
use App\Modules\Marketplace\Domain\Entities\AvitoProductEntity;
use Illuminate\Pagination\LengthAwarePaginator;

interface AvitoProductRepositoryInterface
{
    public function getById(int $id): AvitoProductEntity;

    public function save(AvitoProductEntity $product): AvitoProductEntity;

    public function delete(int $id): void;

    /**
     * Отфильтрованный список товаров Авито.
     * Репозиторий применяет только непустые поля фильтра и пишет в $filter->count
     * число применённых фильтров, а в $filter->active/$filter->archive — счётчики.
     *
     * @return LengthAwarePaginator<AvitoProductEntity>
     */
    public function getFilteredPaginated(FilterAvitoProductIndexData &$filter): LengthAwarePaginator;

    public function countActive(): int;

    public function countArchived(): int;
}
