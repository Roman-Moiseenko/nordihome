<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\DTOs\AttributeGroup\AttributeGroupAttributeData;
use App\Modules\Catalog\Domain\Entities\AttributeGroupEntity;
use App\Modules\Catalog\Domain\Interfaces\AttributeGroupRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Models\Attribute;
use App\Modules\Catalog\Infrastructure\Models\AttributeGroup;

class AttributeGroupRepository implements AttributeGroupRepositoryInterface
{
    /**
     * @return AttributeGroupEntity[]
     */
    public function getAll(): array
    {
        return AttributeGroup::orderBy('sort')
            ->get()
            ->map(fn(AttributeGroup $model) => $this->hydrate($model))
            ->all();
    }

    public function getById(int $id): AttributeGroupEntity
    {
        return $this->hydrate(AttributeGroup::findOrFail($id));
    }

    public function getNamesByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        return AttributeGroup::whereIn('id', $ids)
            ->pluck('name', 'id')
            ->toArray();
    }

    public function save(AttributeGroupEntity $group): AttributeGroupEntity
    {
        $model = $group->id
            ? AttributeGroup::findOrFail($group->id)
            : new AttributeGroup();

        $model->name = $group->name;
        $model->svg = $group->svg;

        if ($group->id === null) {
            $model->sort = ((int) AttributeGroup::max('sort')) + 1;
        }

        $model->save();

        return $this->hydrate($model->fresh());
    }

    public function delete(int $id): void
    {
        AttributeGroup::findOrFail($id)->delete();
    }

    public function countAttributesByGroupIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        return Attribute::whereIn('group_id', $ids)
            ->selectRaw('group_id, count(*) as aggregate')
            ->groupBy('group_id')
            ->pluck('aggregate', 'group_id')
            ->map(fn($value) => (int) $value)
            ->toArray();
    }

    public function getAttributes(int $groupId): array
    {
        return Attribute::where('group_id', $groupId)
            ->orderBy('name')
            ->get()
            ->map(fn(Attribute $attribute) => new AttributeGroupAttributeData(
                id: $attribute->id,
                name: $attribute->name,
                type_text: $attribute->typeText(),
                filter: (bool) $attribute->filter,
            ))
            ->all();
    }

    public function hasAttributes(int $groupId): bool
    {
        return Attribute::where('group_id', $groupId)->exists();
    }

    public function updateSortOrder(int $groupId, int $newSort): void
    {
        $model = AttributeGroup::findOrFail($groupId);

        $currentSort = (int) $model->sort;

        if ($currentSort === $newSort) {
            return;
        }

        if ($newSort < $currentSort) {
            AttributeGroup::where('id', '!=', $groupId)
                ->whereBetween('sort', [$newSort, $currentSort])
                ->increment('sort');
        } else {
            AttributeGroup::where('id', '!=', $groupId)
                ->whereBetween('sort', [$currentSort, $newSort])
                ->decrement('sort');
        }

        $model->sort = $newSort;
        $model->save();
    }

    private function hydrate(AttributeGroup $model): AttributeGroupEntity
    {
        $entity = new AttributeGroupEntity(
            name: $model->name,
            svg: $model->svg,
        );

        $entity->id = $model->id;
        $entity->sort = (int) ($model->sort ?? 0);

        return $entity;
    }
}
