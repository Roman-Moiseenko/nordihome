<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\DTOs\Equivalent\FilterEquivalentIndexData;
use App\Modules\Catalog\Domain\Entities\EquivalentEntity;
use App\Modules\Catalog\Domain\Interfaces\EquivalentRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Models\Equivalent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class EquivalentRepository implements EquivalentRepositoryInterface
{
    public function getById(int $id): EquivalentEntity
    {
        $model = Equivalent::find($id);

        if (!$model) {
            throw new ModelNotFoundException("Equivalent with id {$id} not found");
        }

        return $this->hydrate($model);
    }

    public function findByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        return Equivalent::whereIn('id', $ids)
            ->get()
            ->map(fn(Equivalent $model) => $this->hydrate($model))
            ->all();
    }

    public function save(EquivalentEntity $equivalent): EquivalentEntity
    {
        $model = $equivalent->id
            ? Equivalent::findOrFail($equivalent->id)
            : new Equivalent();

        $model->name = $equivalent->name;
        $model->category_id = $equivalent->categoryId;

        $model->save();

        return $this->hydrate($model->fresh());
    }

    public function delete(int $id): void
    {
        $model = Equivalent::find($id);

        if (!$model) {
            throw new ModelNotFoundException("Equivalent with id {$id} not found");
        }

        $model->delete();
    }

    public function filteredPaginated(FilterEquivalentIndexData &$filter): LengthAwarePaginator
    {
        $query = Equivalent::orderByDesc('id');

        $filter->count = 0;

        if (!is_null($filter->name) && trim($filter->name) !== '') {
            $name = trim($filter->name);
            $query->whereRaw("LOWER(name) LIKE LOWER(?)", ["%{$name}%"]);
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

        if (!is_null($filter->category)) {
            $query->where('category_id', $filter->category);
            $filter->count++;
        }

        return $query->paginate($filter->perPage)
            ->through(fn(Equivalent $model) => $this->hydrate($model));
    }

    private function hydrate(Equivalent $model): EquivalentEntity
    {
        $entity = new EquivalentEntity(
            name: $model->name,
            categoryId: $model->category_id,
        );

        $entity->id = $model->id;

        return $entity;
    }
}
