<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Interfaces;

use App\Modules\Accounting\Application\DTOs\Stock\FilterStockIndexData;
use App\Modules\Accounting\Domain\Entities\StockEntity;
use App\Modules\Accounting\Domain\Entities\StockItemEntity;
use Illuminate\Pagination\LengthAwarePaginator;

interface StockRepositoryInterface
{
    /** @return StockEntity[] */
    public function getAll(): array;

    public function getById(int $id): StockEntity;

    /** @return StockEntity[] */
    public function getAllEmpty(): array;

    public function findByProductId(int $productId): ?StockEntity;

    public function save(StockEntity $stock): StockEntity;

    /** @return LengthAwarePaginator<StockItemEntity> */
    public function getFilteredPaginated(FilterStockIndexData &$filter): LengthAwarePaginator;
}
