<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Domain\Entities\SeriesEntity;
use App\Modules\Catalog\Domain\Interfaces\SeriesRepositoryInterface;
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
