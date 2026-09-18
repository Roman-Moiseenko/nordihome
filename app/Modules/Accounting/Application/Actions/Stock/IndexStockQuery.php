<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Stock;

use App\Modules\Accounting\Application\DTOs\Stock\FilterStockIndexData;
use App\Modules\Accounting\Application\DTOs\Stock\StockIndexData;
use App\Modules\Accounting\Domain\Entities\StockItemEntity;
use App\Modules\Accounting\Domain\Interfaces\StockRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class IndexStockQuery
{
    public function __construct(
        private StockRepositoryInterface $stockRepository,
    ) {
    }

    /** @return LengthAwarePaginator<StockIndexData> */
    public function execute(FilterStockIndexData &$filter, UserPermission $permission): LengthAwarePaginator
    {
        if (!$permission->can('accounting.stock.view')) throw new AccessDeniedException();


        return $this->stockRepository
            ->getFilteredPaginated($filter)
            ->through(fn(StockItemEntity $item) => StockIndexData::fromEntity($item));
    }
}
