<?php

namespace App\Modules\Marketplace\Infrastructure\Persistence;

use App\Modules\Marketplace\Domain\Entities\AvitoProductValueEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductValueRepositoryInterface;
use App\Modules\Marketplace\Infrastructure\Models\AvitoProductValue;

class AvitoProductValueRepository implements AvitoProductValueRepositoryInterface
{
    public function getByProductId(int $avitoProductId): array
    {
        return AvitoProductValue::where('avito_product_id', $avitoProductId)
            ->orderBy('id')
            ->get()
            ->map(fn(AvitoProductValue $model) => $this->hydrate($model))
            ->all();
    }

    public function save(AvitoProductValueEntity $value): AvitoProductValueEntity
    {
        $model = $value->id
            ? AvitoProductValue::findOrFail($value->id)
            : new AvitoProductValue();

        $model->avito_product_id = $value->avitoProductId;
        $model->avito_category_field_id = $value->avitoCategoryFieldId;
        $model->value = $value->value;
        $model->save();

        return $this->hydrate($model);
    }

    public function deleteByProductId(int $avitoProductId): void
    {
        AvitoProductValue::where('avito_product_id', $avitoProductId)->delete();
    }

    private function hydrate(AvitoProductValue $model): AvitoProductValueEntity
    {
        $entity = new AvitoProductValueEntity(
            avitoProductId: $model->avito_product_id,
            avitoCategoryFieldId: $model->avito_category_field_id,
            value: $model->value,
        );

        $entity->id = $model->id;

        return $entity;
    }
}
