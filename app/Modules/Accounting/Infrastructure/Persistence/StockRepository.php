<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Persistence;

use App\Modules\Accounting\Domain\Entities\StockEntity;
use App\Modules\Accounting\Domain\Interfaces\StockRepositoryInterface;
use App\Modules\Accounting\Infrastructure\Models\Stock;
use DateTimeImmutable;

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
}
