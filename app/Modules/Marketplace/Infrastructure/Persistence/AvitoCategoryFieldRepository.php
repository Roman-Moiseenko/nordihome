<?php

namespace App\Modules\Marketplace\Infrastructure\Persistence;

use App\Modules\Marketplace\Domain\Entities\AvitoCategoryFieldEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryFieldRepositoryInterface;
use App\Modules\Marketplace\Infrastructure\Models\AvitoCategoryField;
use Illuminate\Support\Facades\DB;

class AvitoCategoryFieldRepository implements AvitoCategoryFieldRepositoryInterface
{
    public function getById(int $id): AvitoCategoryFieldEntity
    {
        return $this->hydrate(AvitoCategoryField::findOrFail($id));
    }

    public function save(AvitoCategoryFieldEntity $field): AvitoCategoryFieldEntity
    {
        $model = $field->id
            ? AvitoCategoryField::findOrFail($field->id)
            : new AvitoCategoryField();

        $model->avito_category_id = $field->avitoCategoryId;
        $model->tag = $field->tag;
        $model->name = $field->name;
        $model->type = $field->type;
        $model->is_required = $field->isRequired;
        $model->allowed_values = $field->allowedValues;
        $model->depends_on = $field->dependsOn;

        $model->save();

        return $this->hydrate($model);
    }

    private function hydrate(AvitoCategoryField $model): AvitoCategoryFieldEntity
    {
        $entity = new AvitoCategoryFieldEntity(
            avitoCategoryId: $model->avito_category_id,
            tag: $model->tag,
            name: $model->name,
            type: $model->type,
            isRequired: (bool) $model->is_required,
            allowedValues: is_array($model->allowed_values) ? $model->allowed_values : null,
            dependsOn: is_array($model->depends_on) ? $model->depends_on : null,
        );

        $entity->id = $model->id;

        return $entity;
    }

    public function countFieldsByCategoryIds(array $categoryIds): array
    {
        if (empty($categoryIds)) {
            return [];
        }

        return AvitoCategoryField::whereIn('avito_category_id', $categoryIds)
            ->selectRaw('avito_category_id, COUNT(*) as count')
            ->groupBy('avito_category_id')
            ->pluck('count', 'avito_category_id')
            ->map(fn($value) => (int) $value)
            ->toArray();
    }

    public function syncForCategory(int $avitoCategoryId, array $fields): void
    {
        DB::transaction(function () use ($avitoCategoryId, $fields) {
            AvitoCategoryField::where('avito_category_id', $avitoCategoryId)->delete();

            foreach ($fields as $fieldData) {
                $field = new AvitoCategoryField();
                $field->avito_category_id = $avitoCategoryId;
                $field->tag = $fieldData['tag'];
                $field->name = $fieldData['name'];
                $field->type = $fieldData['type'] ?? null;
                $field->is_required = $fieldData['is_required'] ?? false;
                $field->allowed_values = $fieldData['allowed_values'] ?? null;
                $field->depends_on = $fieldData['depends_on'] ?? null;
                $field->save();
            }
        });
    }

    public function upsertForCategory(int $avitoCategoryId, array $fields): void
    {
        DB::transaction(function () use ($avitoCategoryId, $fields) {
            foreach ($fields as $fieldData) {
                $field = AvitoCategoryField::where('avito_category_id', $avitoCategoryId)
                    ->where('tag', $fieldData['tag'])
                    ->first();

                if ($field === null) {
                    $field = new AvitoCategoryField();
                    $field->avito_category_id = $avitoCategoryId;
                    $field->tag = $fieldData['tag'];
                    $field->name = $fieldData['name'];
                    $field->type = $fieldData['type'] ?? null;
                    $field->is_required = $fieldData['is_required'] ?? false;
                    $field->allowed_values = $fieldData['allowed_values'] ?? null;
                    $field->depends_on = $fieldData['depends_on'] ?? null;
                    $field->save();
                    continue;
                }

                $field->name = $fieldData['name'];
                $field->type = $fieldData['type'] ?? null;
                $field->is_required = $fieldData['is_required'] ?? false;

                $incoming = $fieldData['allowed_values'] ?? [];
                if (!empty($incoming)) {
                    $existing = is_array($field->allowed_values) ? $field->allowed_values : [];
                    $field->allowed_values = array_values(array_unique(array_merge($existing, $incoming)));
                }

                $field->save();
            }
        });
    }

    public function getByCategoryId(int $avitoCategoryId): array
    {
        return AvitoCategoryField::where('avito_category_id', $avitoCategoryId)
            ->orderBy('name')
            ->get()
            ->map(fn(AvitoCategoryField $model) => [
                'id' => $model->id,
                'tag' => $model->tag,
                'name' => $model->name,
                'type' => $model->type,
                'is_required' => (bool) $model->is_required,
                'allowed_values' => is_array($model->allowed_values) ? $model->allowed_values : [],
            ])
            ->all();
    }
}
