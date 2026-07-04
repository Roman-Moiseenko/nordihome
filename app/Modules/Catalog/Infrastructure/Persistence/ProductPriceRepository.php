<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\Interfaces\ProductPriceRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\ProductPriceEntity;
use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use App\Modules\Catalog\Infrastructure\Models\ProductPrice;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

class ProductPriceRepository implements ProductPriceRepositoryInterface
{
    public function getById(int $id): ProductPriceEntity
    {
        $model = ProductPrice::findOrFail($id);
        return $this->hydrate($model);
    }

    public function getLastByProductAndType(int $productId, string $type): ?ProductPriceEntity
    {
        $model = ProductPrice::where('product_id', $productId)
            ->where('type', $type)
            ->orderBy('set_at', 'desc')
            ->first();

        if ($model === null) {
            return null;
        }

        return $this->hydrate($model);
    }

    public function getByProductId(int $productId): array
    {
        return ProductPrice::where('product_id', $productId)
            ->orderBy('set_at', 'desc')
            ->get()
            ->map(fn(ProductPrice $model) => $this->hydrate($model))
            ->all();
    }

    public function findCurrentPrices(int $productId): array
    {
        $sub = DB::table('product_prices')
            ->selectRaw('MAX(set_at) as latest_date, type')
            ->where('product_id', $productId)
            ->groupBy('type');

        $rows = DB::table('product_prices')
            ->joinSub($sub, 'latest', function ($join) {
                $join->on('product_prices.type', '=', 'latest.type')
                    ->on('product_prices.set_at', '=', 'latest.latest_date');
            })
            ->where('product_prices.product_id', $productId)
            ->get();

        $prices = [];
        foreach ($rows as $row) {
            $prices[$row->type] = (float) $row->amount;
        }

        return $prices;
    }

    public function save(ProductPriceEntity $price): ProductPriceEntity
    {
        $model = $price->id
            ? ProductPrice::findOrFail($price->id)
            : new ProductPrice();

        $model->product_id = $price->productId;
        $model->type = $price->priceType->value;
        $model->amount = $price->price;
        $model->currency = 'RUB';
        $model->set_at = $price->setAt;
        $model->founded = $price->founded;
        $model->comment = $price->comment;

        $model->save();

        if ($price->id === null) {
            $price->id = $model->id;
        }

        return $price;
    }

    public function delete(int $id): void
    {
        $model = ProductPrice::findOrFail($id);
        $model->delete();
    }

    private function hydrate(ProductPrice $model): ProductPriceEntity
    {
        $setAt = $model->set_at instanceof DateTimeImmutable
            ? $model->set_at
            : new DateTimeImmutable($model->set_at->toIso8601String());

        $entity = new ProductPriceEntity(
            productId: $model->product_id,
            price: (float) $model->amount,
            priceType: PriceType::fromString($model->type),
            setAt: $setAt,
        );

        $entity->id = $model->id;
        $entity->founded = $model->founded;
        $entity->comment = $model->comment;

        return $entity;
    }
}
