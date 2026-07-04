<?php

declare(strict_types=1);

namespace App\Modules\Parser\Infrastructure\Persistence;

use App\Modules\Parser\Application\Interfaces\ParserCategoryRepositoryInterface;
use App\Modules\Parser\Domain\Entities\ParserCategoryEntity;
use App\Modules\Parser\Infrastructure\Models\ParserCategory;

class ParserCategoryRepository implements ParserCategoryRepositoryInterface
{
    /**
     * @return ParserCategoryEntity[]
     */
    public function getAll(): array
    {
        return ParserCategory::defaultOrder()
            ->get()
            ->map(fn(ParserCategory $model) => $this->hydrate($model))
            ->values()
            ->toArray();
    }

    public function getById(int $id): ParserCategoryEntity
    {
        $model = ParserCategory::findOrFail($id);
        return $this->hydrate($model);
    }

    public function getByIkeaId(string $ikeaId):? ParserCategoryEntity
    {
        $model = ParserCategory::where('ikea_id', $ikeaId)->first();
        if (is_null($model)) return null;
        return $this->hydrate($model);
    }

    public function save(ParserCategoryEntity $category): ParserCategoryEntity
    {
        $model = $category->id
            ? ParserCategory::findOrFail($category->id)
            : new ParserCategory();

        $model->name = $category->name;
        $model->slug = $category->slug;
        $model->ikea_id = $category->ikeaId;
        $model->active = $category->isActive();

        if ($category->parentId !== null) {
            $model->parent_id = $category->parentId;
        } elseif ($category->id === null) {
            $model->parent_id = null;
        }

        $model->save();

        return $this->hydrate($model);
    }

    public function delete(int $id): void
    {
        $model = ParserCategory::findOrFail($id);
        $model->delete();
    }

    /**
     * @return ParserCategoryEntity[]
     */
    public function getTree(): array
    {
        $roots = ParserCategory::defaultOrder()
            ->get()
            ->toTree();

        return $roots
            ->map(fn(ParserCategory $model) => $this->hydrateWithChildren($model))
            ->toArray();
    }

    public function existsByIkeaId(string $ikeaId): bool
    {
        return ParserCategory::where('ikea_id', $ikeaId)->exists();
    }

    public function findByIkeaId(string $ikeaId): ?ParserCategoryEntity
    {
        $model = ParserCategory::where('ikea_id', $ikeaId)->first();

        if ($model === null) {
            return null;
        }

        return $this->hydrate($model);
    }

    public function existsSlug(string $slug, ?int $excludeId = null): bool
    {
        $query = ParserCategory::where('slug', $slug);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * @return ParserCategoryEntity[]
     */
    public function getActiveRoots(): array
    {
        return ParserCategory::where('active', true)
            ->whereNull('parent_id')
            ->defaultOrder()
            ->get()
            ->map(fn(ParserCategory $model) => $this->hydrate($model))
            ->toArray();
    }

    /**
     * @return ParserCategoryEntity[]
     */
    public function getActiveLeaves(): array
    {
        // Листовые категории — те, у которых нет детей (не являются родителями для других)
        $parentIds = ParserCategory::where('active', true)
            ->whereNotNull('parent_id')
            ->pluck('parent_id')
            ->unique()
            ->toArray();

        return ParserCategory::where('active', true)
            ->whereNotIn('id', $parentIds)
            ->defaultOrder()
            ->get()
            ->map(fn(ParserCategory $model) => $this->hydrate($model))
            ->toArray();
    }

    public function hasChildren(int $id): bool
    {
        return ParserCategory::where('parent_id', $id)->exists();
    }

    /**
     * @return int[]
     */
    public function getDescendantIds(int $id): array
    {
        $model = ParserCategory::findOrFail($id);

        return ParserCategory::where('_lft', '>', $model->_lft)
            ->where('_rgt', '<', $model->_rgt)
            ->pluck('id')
            ->toArray();
    }

    public function toggleActive(int $id): void
    {
        $model = ParserCategory::findOrFail($id);

        $newActive = !$model->active;

        // Меняем состояние для всех дочерних категорий и текущей
        ParserCategory::where('_lft', '>=', $model->_lft)
            ->where('_rgt', '<=', $model->_rgt)
            ->update(['active' => $newActive]);
    }

    public function bulkToggleActive(array $ids, bool $active): void
    {
        if (empty($ids)) {
            return;
        }

        ParserCategory::whereIn('id', $ids)->update(['active' => $active]);
    }

    // ============================================================
    //  Hydrate — преобразование Eloquent Model -> Domain Entity
    // ============================================================

    private function hydrate(ParserCategory $model): ParserCategoryEntity
    {
        $entity = new ParserCategoryEntity(
            name: $model->name,
            slug: $model->slug ?? '',
            ikeaId: $model->ikea_id,
            parentId: $model->parent_id,
        );

        $entity->id = $model->id;
        $entity->active = $model->active;

        // Nested Set поля
        $entity->left = $model->_lft ?? 0;
        $entity->right = $model->_rgt ?? 0;

        return $entity;
    }

    /**
     * Гидратация с рекурсивным заполнением children и parent.
     */
    private function hydrateWithChildren(ParserCategory $model): ParserCategoryEntity
    {
        $entity = $this->hydrate($model);

        if ($model->children !== null && $model->children->isNotEmpty()) {
            $children = array_map(
                fn(ParserCategory $child) => $this->hydrateWithChildren($child),
                $model->children->all()
            );

            $entity->children = $children;
        }

        return $entity;
    }
}
