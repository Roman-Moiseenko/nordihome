<?php

namespace App\Modules\Guide\Infrastructure\Persistence;

use App\Modules\Guide\Domain\Entities\AdditionEntity;
use App\Modules\Guide\Domain\Interfaces\AdditionRepositoryInterface;
use App\Modules\Guide\Domain\ValueObjects\AdditionType;
use App\Modules\Guide\Infrastructure\Models\Addition;
use App\Modules\Shared\Domain\ValueObjects\Slug;

class AdditionRepository implements AdditionRepositoryInterface
{

    public function getById(int $id): AdditionEntity
    {
        $model = Addition::findOrFail($id);
        return $this->hydrate($model);
    }

    public function getAll(): array
    {
        $models = Addition::orderBy('name')->getModels();
        return array_map(function (Addition $addition) {
            return $this->hydrate($addition);
        }, $models);
    }

    public function findBySlug(string $slug): ?AdditionEntity
    {
        $model = Addition::where('slug', $slug)->first();
        return is_null($model) ? null : $this->hydrate($model);
    }
    /**
     * @return AdditionEntity[]
     */
    public function getByType(int|string $type): array
    {
        $models = Addition::orderBy('name')->where('type', $type)->getModels();
        return array_map(function (Addition $addition) {
            return $this->hydrate($addition);
        }, $models);
    }

    public function remove(int $id): void
    {
        Addition::deleted($id);
    }
    public function save(AdditionEntity $addition): AdditionEntity
    {
        $model = $addition->id
            ? Addition::findOrFail($addition->id)
            : new Addition();
        $model->name = $addition->name;
        $model->slug = $addition->slug->getValue();
        $model->type = $addition->type->value;
        $model->class = $addition->class;
        $model->base = $addition->base;
        $model->manual = $addition->manual;
        $model->is_quantity = $addition->isQuantity;

        $model->save();

        return $this->hydrate($model->fresh());
    }


    private function hydrate(Addition $model): AdditionEntity
    {
        $addition = new AdditionEntity(
            name: $model->name,
            slug: new Slug($model->slug),
            type: new AdditionType($model->type),
        );
        $addition->id = $model->id;
        $addition->class = $model->class;
        $addition->isQuantity = $model->is_quantity;
        $addition->manual = $model->manual;
        $addition->base = $model->base;

        return $addition;
    }
}
