<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Persistence;

use App\Modules\Accounting\Application\DTOs\Stock\FilterStockIndexData;
use App\Modules\Accounting\Domain\Entities\StockEntity;
use App\Modules\Accounting\Domain\Entities\StockItemEntity;
use App\Modules\Accounting\Domain\Interfaces\StockRepositoryInterface;
use App\Modules\Accounting\Infrastructure\Models\Stock;
use DateTimeImmutable;
use Illuminate\Pagination\LengthAwarePaginator;

class StockRepository implements StockRepositoryInterface
{
    public function getAll(): array
    {
        return Stock::query()
            ->get()
            ->map(fn(Stock $model) => $this->hydrate($model))
            ->all();
    }

    public function getById(int $id): StockEntity
    {
        return $this->hydrate(Stock::query()->findOrFail($id));
    }

    public function getAllEmpty(): array
    {
        return Stock::query()
            ->where('quantity', 0)
            ->get()
            ->map(fn(Stock $model) => $this->hydrate($model))
            ->all();
    }

    public function findByProductId(int $productId): ?StockEntity
    {
        $model = Stock::query()->where('product_id', $productId)->first();

        return $model ? $this->hydrate($model) : null;
    }

    public function getFilteredPaginated(FilterStockIndexData &$filter): LengthAwarePaginator
    {
        $query = Stock::query()
            ->join('products', 'products.id', '=', 'stock.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.main_category_id')
            ->select([
                'products.id as product_id',
                'products.code as code',
                'categories.name as category_name',
                'stock.quantity as quantity',
                'stock.reserve as reserve',
            ]);

        $filter->count = 0;

        if (!is_null($filter->code) && trim($filter->code) !== '') {
            $code = trim($filter->code);
            $query->where('products.code', 'like', "%{$code}%");
            $filter->count++;
        }

        if (!is_null($filter->categoryId)) {
            $query->where('products.main_category_id', $filter->categoryId);
            $filter->count++;
        }

        return $query
            ->orderBy('products.code')
            ->paginate($filter->perPage)
            ->withQueryString()
            ->through(fn(Stock $model) => $this->hydrateItem($model));
    }

    public function save(StockEntity $stock): StockEntity
    {
        $model = $stock->id
            ? Stock::query()->findOrFail($stock->id)
            : new Stock();

        $model->product_id = $stock->productId;
        $model->quantity = $stock->quantity;
        $model->reserve = $stock->reserve;
        $model->updated_at = now();

        $model->save();

        return $this->hydrate($model->fresh());
    }

    private function hydrate(Stock $model): StockEntity
    {
        $stock = new StockEntity(
            productId: $model->product_id,
            quantity: $model->quantity,
            reserve: $model->reserve,
        );

        $stock->id = $model->id;

        if ($model->updated_at) {
            $stock->updatedAt = DateTimeImmutable::createFromMutable($model->updated_at);
        }

        return $stock;
    }

    private function hydrateItem(Stock $model): StockItemEntity
    {
        return new StockItemEntity(
            id: (int) $model->product_id,
            code: (string) $model->code,
            categoryName: (string) ($model->category_name ?? ''),
            quantity: (int) $model->quantity,
            reserve: (int) $model->reserve,
        );
    }
}
