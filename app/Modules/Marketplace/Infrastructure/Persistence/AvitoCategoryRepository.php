<?php

namespace App\Modules\Marketplace\Infrastructure\Persistence;

use App\Modules\Marketplace\Domain\Entities\AvitoCategoryEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryRepositoryInterface;
use App\Modules\Marketplace\Infrastructure\Models\AvitoCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AvitoCategoryRepository implements AvitoCategoryRepositoryInterface
{
    /**
     * @return AvitoCategoryEntity[]
     */
    public function getTree(): array
    {
        $roots = AvitoCategory::defaultOrder()
            ->withDepth()
            ->get()
            ->toTree();

        return $roots->map(fn(AvitoCategory $model) => $this->hydrateWithChildren($model))->all();
    }

    public function getById(int $id): AvitoCategoryEntity
    {
        $model = AvitoCategory::findOrFail($id);

        return $this->hydrate($model);
    }

    public function getBySlug(string $slug): ?AvitoCategoryEntity
    {
        $model = AvitoCategory::where('slug', $slug)->first();

        return $model === null ? null : $this->hydrate($model);
    }

    public function save(AvitoCategoryEntity $category): AvitoCategoryEntity
    {
        $model = $category->id
            ? AvitoCategory::findOrFail($category->id)
            : new AvitoCategory();

        $model->name = $category->name;
        $model->slug = $category->slug;
        $model->parent_id = $category->parentId;

        $model->save();

        return $this->hydrate($model);
    }

    public function delete(int $id): void
    {
        $model = AvitoCategory::findOrFail($id);
        $model->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function sync(array $nodes): void
    {
        $incomingSlugs = array_column($nodes, 'slug');

        DB::transaction(function () use ($nodes, $incomingSlugs) {
            // Удаляем категории, которых больше нет на Авито.
            AvitoCategory::whereNotIn('slug', $incomingSlugs)->delete();

            $idBySlug = AvitoCategory::pluck('id', 'slug')->all();

            foreach ($nodes as $node) {
                $parentId = $node['parent_slug'] !== null
                    ? ($idBySlug[$node['parent_slug']] ?? null)
                    : null;

                AvitoCategory::updateOrCreate(
                    ['slug' => $node['slug']],
                    ['name' => $node['name'], 'parent_id' => $parentId],
                );
            }

            // Пересобираем NestedSet после массовых изменений parent_id.
            AvitoCategory::fixTree();
        });
    }

    /**
     * @return AvitoCategoryEntity[]
     */
    public function getLeaves(): array
    {
        return AvitoCategory::whereDoesntHave('children')
            ->get()
            ->map(fn(AvitoCategory $model) => $this->hydrate($model))
            ->all();
    }

    public function getPathNamesByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $result = [];
        $categories = AvitoCategory::whereIn('id', $ids)
            ->with('ancestors')
            ->get();

        foreach ($categories as $category) {
            $names = $category->ancestors->pluck('name')->all();
            $names[] = $category->name;
            $result[$category->id] = implode('\\', $names);
        }

        return $result;
    }

    public function findByNames(array $names): ?AvitoCategoryEntity
    {
        $parentId = null;
        $entity = null;

        foreach ($names as $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }

            $model = AvitoCategory::where('parent_id', $parentId)
                ->where('name', $name)
                ->first();

            if ($model === null) {
                return null;
            }

            $parentId = $model->id;
            $entity = $this->hydrate($model);
        }

        return $entity;
    }

    public function findOrCreateByNames(array $names): AvitoCategoryEntity
    {
        $parentId = null;
        $entity = null;

        foreach ($names as $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }

            $model = AvitoCategory::where('parent_id', $parentId)
                ->where('name', $name)
                ->first();

            if ($model === null) {
                $model = new AvitoCategory();
                $model->name = $name;
                $model->slug = $this->uniqueSlug(Str::slug($name));
                $model->parent_id = $parentId;
                $model->save();
            }

            $parentId = $model->id;
            $entity = $this->hydrate($model);
        }

        return $entity;
    }

    public function fixTree(): void
    {
        AvitoCategory::fixTree();
    }

    private function uniqueSlug(string $slug): string
    {
        $base = $slug === '' ? 'category' : $slug;
        $candidate = $base;
        $i = 1;

        while (AvitoCategory::where('slug', $candidate)->exists()) {
            $candidate = $base . '-' . $i;
            $i++;
        }

        return $candidate;
    }

    private function hydrate(AvitoCategory $model): AvitoCategoryEntity
    {
        $entity = new AvitoCategoryEntity(
            name: $model->name,
            slug: $model->slug,
            parentId: $model->parent_id,
        );

        $entity->id = $model->id;
        $entity->left = $model->_lft ?? 0;
        $entity->right = $model->_rgt ?? 0;
        $entity->depth = $model->depth ?? 0;

        return $entity;
    }

    private function hydrateWithChildren(AvitoCategory $model): AvitoCategoryEntity
    {
        $entity = $this->hydrate($model);

        if ($model->children !== null && $model->children->isNotEmpty()) {
            $entity->children = array_map(
                fn(AvitoCategory $child) => $this->hydrateWithChildren($child),
                $model->children->all()
            );
        }

        return $entity;
    }
}
