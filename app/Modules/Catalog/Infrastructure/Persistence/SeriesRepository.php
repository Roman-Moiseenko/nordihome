<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\DTOs\Series\FilterSeriesIndexData;
use App\Modules\Catalog\Domain\Entities\SeriesEntity;
use App\Modules\Catalog\Domain\Interfaces\SeriesRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Catalog\Infrastructure\Models\Series;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class SeriesRepository implements SeriesRepositoryInterface
{
    public function getAll(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return Series::orderBy('name')
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(fn(Series $model) => $this->hydrate($model));
    }

    public function getById(int $id): SeriesEntity
    {
        $model = Series::find($id);

        if (!$model) {
            throw new ModelNotFoundException("Series with id {$id} not found");
        }

        return $this->hydrate($model);
    }

    public function save(SeriesEntity $series): SeriesEntity
    {
        $model = $series->id
            ? Series::findOrFail($series->id)
            : new Series();

        $model->name = $series->name;
        $model->name_ru = $series->nameRu;

        $model->save();

        return $this->hydrate($model->fresh());
    }

    public function delete(int $id): void
    {
        $model = Series::find($id);

        if (!$model) {
            throw new ModelNotFoundException("Series with id {$id} not found");
        }

        $model->delete();
    }

    public function getByName(string $name): ?SeriesEntity
    {
        $model = Series::where('name', $name)->first();

        if (!$model) {
            return null;
        }

        return $this->hydrate($model);
    }

    public function filteredPaginated(FilterSeriesIndexData &$filter): LengthAwarePaginator
    {
        $query = Series::orderByDesc('id');

        $filter->count = 0;

        if (!is_null($filter->series) && trim($filter->series) !== '') {
            $series = trim($filter->series);
            $query->whereRaw("LOWER(name) LIKE LOWER(?)", ["%{$series}%"]);
            $filter->count++;
        }

        if (!is_null($filter->product) && trim($filter->product) !== '') {
            $product = trim($filter->product);
            $query->whereHas('products', function ($q) use ($product) {
                $q->whereRaw("LOWER(name) LIKE LOWER(?)", ["%{$product}%"])
                    ->orWhere('code', 'like', "%{$product}%")
                    ->orWhere('code_search', 'like', "%{$product}%");
            });
            $filter->count++;
        }

        return $query->paginate($filter->perPage)
            ->through(fn(Series $model) => $this->hydrate($model));
    }

    public function getProducts(int $seriesId): array
    {
        $model = Series::find($seriesId);

        if (!$model) {
            throw new ModelNotFoundException("Series with id {$seriesId} not found");
        }

        return $model->products()->with('category')->get()->map(
            fn(Product $product) => [
                'id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'category' => $product->category?->getParentNames() ?? '',
            ]
        )->toArray();
    }

    public function attachProducts(int $seriesId, array $productIds): void
    {
        $productIds = array_values(array_unique(array_map('intval', $productIds)));

        if (empty($productIds)) {
            return;
        }

        $existing = Product::where('series_id', $seriesId)
            ->whereIn('id', $productIds)
            ->pluck('id')
            ->toArray();

        $new = array_values(array_diff($productIds, $existing));

        if (empty($new)) {
            return;
        }

        Product::whereIn('id', $new)->update(['series_id' => $seriesId]);
    }

    public function detachProduct(int $seriesId, int $productId): void
    {
        $product = Product::find($productId);

        if (!$product) {
            return;
        }

        if ((int) $product->series_id !== $seriesId) {
            throw new \DomainException('Не совпадение серий');
        }

        $product->series_id = null;
        $product->save();
    }

    public function detachAllProducts(int $seriesId): void
    {
        Product::where('series_id', $seriesId)->update(['series_id' => null]);
    }

    private function hydrate(Series $model): SeriesEntity
    {
        $entity = new SeriesEntity(
            name: $model->name,
            nameRu: $model->name_ru ?? '',
        );

        $entity->id = $model->id;

        return $entity;
    }
}
