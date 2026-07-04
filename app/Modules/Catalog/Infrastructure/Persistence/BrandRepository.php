<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\Interfaces\BrandRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\BrandEntity;
use App\Modules\Catalog\Infrastructure\Models\Brand;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BrandRepository implements BrandRepositoryInterface
{
    /**
     * @return BrandEntity[]
     */
    public function getAll(): array
    {
        $models = Brand::orderBy('name')->get();

        return $models->map(fn(Brand $model) => $this->hydrate($model))->toArray();
    }

    public function getById(int $id): BrandEntity
    {
        $model = Brand::find($id);

        if (!$model) {
            throw new ModelNotFoundException("Brand with id {$id} not found");
        }

        return $this->hydrate($model);
    }

    public function save(BrandEntity $brand): BrandEntity
    {
        $model = $brand->id
            ? Brand::findOrFail($brand->id)
            : new Brand();

        $model->name = $brand->name;
        $model->description = $brand->description;
        $model->url = $brand->url;
        $model->currency_id = $brand->currencyId;
        $model->parser_class = $brand->parserClass;
        $model->sameas_json = $brand->sameAs;

        $model->save();

        return $this->hydrate($model->fresh());
    }

    public function delete(int $id): void
    {
        $model = Brand::find($id);

        if (!$model) {
            throw new ModelNotFoundException("Brand with id {$id} not found");
        }

        $model->delete();
    }

    public function getByName(string $name): ?BrandEntity
    {
        $model = Brand::where('name', $name)->first();

        if (!$model) {
            return null;
        }

        return $this->hydrate($model);
    }

    /**
     * @return BrandEntity[]
     */
    public function searchByName(string $name): array
    {
        $models = Brand::orderBy('name')
            ->whereRaw("LOWER(name) LIKE LOWER(?)", ["%{$name}%"])
            ->get();

        return $models->map(fn(Brand $model) => $this->hydrate($model))->toArray();
    }

    public function getIkeaId(): int
    {
        $model = Brand::where('name', BrandEntity::IKEA)->first();

        if (!$model) {
            throw new ModelNotFoundException("Brand '" . BrandEntity::IKEA . "' not found");
        }

        return $model->id;
    }

    public function getNbId(): int
    {
        $model = Brand::where('name', BrandEntity::NB)->first();

        if (!$model) {
            throw new ModelNotFoundException("Brand '" . BrandEntity::NB . "' not found");
        }

        return $model->id;
    }

    private function hydrate(Brand $model): BrandEntity
    {
        $entity = new BrandEntity(
            name: $model->name,
            description: $model->description ?? '',
            url: $model->url ?? '',
        );

        $entity->id = $model->id;
        $entity->currencyId = $model->currency_id;
        $entity->parserClass = $model->parser_class;
        $entity->sameAs = is_array($model->sameas_json) ? $model->sameas_json : [];

        return $entity;
    }

}
