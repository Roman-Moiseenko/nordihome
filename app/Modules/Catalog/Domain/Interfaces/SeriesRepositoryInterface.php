<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Interfaces;

use App\Modules\Catalog\Application\DTOs\Series\FilterSeriesIndexData;
use App\Modules\Catalog\Domain\Entities\SeriesEntity;
use Illuminate\Pagination\LengthAwarePaginator;

interface SeriesRepositoryInterface
{
    public function getAll(int $perPage = 15, int $page = 1): LengthAwarePaginator;

    public function getById(int $id): SeriesEntity;

    public function save(SeriesEntity $series): SeriesEntity;

    public function delete(int $id): void;

    public function getByName(string $name): ?SeriesEntity;

    public function filteredPaginated(FilterSeriesIndexData &$filter): LengthAwarePaginator;

    /**
     * Список товаров серии (для карточки серии).
     *
     * @return array<int, array{id: int, code: string, name: string, category: string}>
     */
    public function getProducts(int $seriesId): array;

    /**
     * Добавить товары в серию. Повторно входящие товары пропускаются.
     *
     * @param int[] $productIds
     */
    public function attachProducts(int $seriesId, array $productIds): void;

    public function detachProduct(int $seriesId, int $productId): void;

    public function detachAllProducts(int $seriesId): void;
}
