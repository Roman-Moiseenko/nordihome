<?php

namespace App\Modules\Marketplace\Infrastructure\Persistence;

use App\Modules\Marketplace\Domain\Entities\AvitoParameterEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoParameterRepositoryInterface;
use App\Modules\Marketplace\Infrastructure\Models\AvitoParameter;

class AvitoParameterRepository implements AvitoParameterRepositoryInterface
{
    /**
     * @return AvitoParameterEntity[]
     */
    public function getAll(): array
    {
        return AvitoParameter::orderBy('id')
            ->get()
            ->map(fn(AvitoParameter $model) => $this->hydrate($model))
            ->all();
    }

    public function getById(int $id): AvitoParameterEntity
    {
        return $this->hydrate(AvitoParameter::findOrFail($id));
    }

    public function getBySlug(string $slug): ?AvitoParameterEntity
    {
        $model = AvitoParameter::where('slug', $slug)->first();

        return $model === null ? null : $this->hydrate($model);
    }

    public function getByNameEn(string $nameEn): ?AvitoParameterEntity
    {
        $model = AvitoParameter::where('name_en', $nameEn)->first();

        return $model === null ? null : $this->hydrate($model);
    }

    public function save(AvitoParameterEntity $parameter): AvitoParameterEntity
    {
        $model = $parameter->id
            ? AvitoParameter::findOrFail($parameter->id)
            : new AvitoParameter();

        $model->name = $parameter->name;
        $model->name_en = $parameter->nameEn;
        $model->slug = $parameter->slug;
        $model->value = $parameter->value;
        $model->variants = $parameter->variants;
        $model->is_html = $parameter->isHtml;

        $model->save();

        return $this->hydrate($model);
    }

    private function hydrate(AvitoParameter $model): AvitoParameterEntity
    {
        $entity = new AvitoParameterEntity(
            name: $model->name,
            slug: $model->slug,
            value: $model->value,
            variants: is_array($model->variants) ? $model->variants : [],
            isHtml: (bool) $model->is_html,
            nameEn: $model->name_en,
        );

        $entity->id = $model->id;

        return $entity;
    }
}
