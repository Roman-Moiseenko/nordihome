<?php

namespace App\Modules\Marketplace\Infrastructure\Persistence;

use App\Modules\Marketplace\Domain\Entities\AvitoParameterRegistryEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoParameterRegistryRepositoryInterface;
use App\Modules\Marketplace\Infrastructure\Models\AvitoParameterRegistry;

class AvitoParameterRegistryRepository implements AvitoParameterRegistryRepositoryInterface
{
    /**
     * @return AvitoParameterRegistryEntity[]
     */
    public function getAll(): array
    {
        return AvitoParameterRegistry::orderBy('name_en')
            ->get()
            ->map(fn (AvitoParameterRegistry $model) => $this->hydrate($model))
            ->all();
    }

    public function getById(int $id): AvitoParameterRegistryEntity
    {
        return $this->hydrate(AvitoParameterRegistry::findOrFail($id));
    }

    public function getByNameEn(string $nameEn): ?AvitoParameterRegistryEntity
    {
        $model = AvitoParameterRegistry::where('name_en', $nameEn)->first();

        return $model === null ? null : $this->hydrate($model);
    }

    public function existsByNameEn(string $nameEn): bool
    {
        return AvitoParameterRegistry::where('name_en', $nameEn)->exists();
    }

    public function save(AvitoParameterRegistryEntity $entity): AvitoParameterRegistryEntity
    {
        $model = $entity->id
            ? AvitoParameterRegistry::findOrFail($entity->id)
            : new AvitoParameterRegistry();

        $model->name_en = $entity->nameEn;
        $model->name_ru = $entity->nameRu;
        $model->kind = $entity->kind;
        $model->field = $entity->field;
        $model->type = $entity->type;
        $model->is_required = $entity->isRequired;

        $model->save();

        return $this->hydrate($model);
    }

    private function hydrate(AvitoParameterRegistry $model): AvitoParameterRegistryEntity
    {
        $entity = new AvitoParameterRegistryEntity(
            nameEn: $model->name_en,
            nameRu: $model->name_ru,
            kind: $model->kind,
            field: $model->field,
            type: $model->type,
            isRequired: (bool) $model->is_required,
        );

        $entity->id = $model->id;

        return $entity;
    }
}
