<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Domain\Entities\SizeGridEntity;
use App\Modules\Catalog\Domain\Entities\SizeGridValueEntity;
use App\Modules\Catalog\Domain\Interfaces\SizeGridRepositoryInterface;
use App\Modules\Catalog\Domain\ValueObjects\SizeGridDimension;
use App\Modules\Catalog\Infrastructure\Models\SizeGrid;
use App\Modules\Catalog\Infrastructure\Models\SizeGridValue;

class SizeGridRepository implements SizeGridRepositoryInterface
{
    /**
     * @return SizeGridEntity[]
     */
    public function getAll(): array
    {
        return SizeGrid::orderBy('name')
            ->with('values')
            ->get()
            ->map(fn(SizeGrid $model) => $this->hydrate($model))
            ->toArray();
    }

    public function getById(int $id): SizeGridEntity
    {
        return $this->hydrate(SizeGrid::with('values')->findOrFail($id));
    }

    public function save(SizeGridEntity $sizeGrid): SizeGridEntity
    {
        $model = $sizeGrid->id
            ? SizeGrid::findOrFail($sizeGrid->id)
            : new SizeGrid();

        $model->name = $sizeGrid->name;
        $model->dimensions = array_map(
            fn(SizeGridDimension $dimension) => $dimension->toArray(),
            $sizeGrid->dimensions,
        );

        $model->save();

        $this->syncValues($model, $sizeGrid->values);

        return $this->hydrate($model->fresh(['values']));
    }

    public function delete(int $id): void
    {
        SizeGrid::findOrFail($id)->delete();
    }

    /**
     * @param SizeGridValueEntity[] $values
     */
    private function syncValues(SizeGrid $model, array $values): void
    {
        $existingIds = $model->values()
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        $keptIds = [];

        $sortCounters = [];

        foreach (array_values($values) as $value) {
            $dimensionCode = $value->dimensionCode;
            $sortOrder = $sortCounters[$dimensionCode] ?? 0;
            $sortCounters[$dimensionCode] = $sortOrder + 1;

            $valueModel = SizeGridValue::updateOrCreate(
                [
                    'size_grid_id' => $model->id,
                    'dimension_code' => $dimensionCode,
                    'value' => $value->value,
                ],
                [
                    'label' => $value->label,
                    'sort_order' => $sortOrder,
                    'is_active' => $value->isActive,
                ],
            );

            $keptIds[] = $valueModel->id;
        }

        $toDelete = array_diff($existingIds, $keptIds);

        if (!empty($toDelete)) {
            $model->values()->whereIn('id', $toDelete)->delete();
        }
    }

    private function hydrate(SizeGrid $model): SizeGridEntity
    {
        $entity = new SizeGridEntity(name: $model->name);
        $entity->id = $model->id;

        $dimensions = is_array($model->dimensions) ? $model->dimensions : [];
        $entity->dimensions = array_map(
            fn(array $dimension) => new SizeGridDimension(
                code: (string) ($dimension['code'] ?? ''),
                name: (string) ($dimension['name'] ?? ''),
            ),
            $dimensions,
        );

        if ($model->relationLoaded('values')) {
            $entity->values = $model->values
                ->sortBy(fn(SizeGridValue $value) => [$value->sort_order, $value->id])
                ->values()
                ->map(fn(SizeGridValue $value) => $this->hydrateValue($value))
                ->toArray();
        }

        return $entity;
    }

    private function hydrateValue(SizeGridValue $model): SizeGridValueEntity
    {
        $value = new SizeGridValueEntity(
            dimensionCode: $model->dimension_code,
            value: $model->value,
            label: $model->label,
            sortOrder: (int) $model->sort_order,
            isActive: (bool) $model->is_active,
        );

        $value->id = $model->id;

        return $value;
    }
}
