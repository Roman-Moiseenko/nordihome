<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\Interfaces\CategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Shared\Domain\ValueObjects\Meta;
use App\Modules\Shared\Domain\ValueObjects\Slug;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @return CategoryEntity[]
     */
    public function getAll(): array
    {
        $models = Category::defaultOrder()->withDepth()->get()->toTree();
        return $models->map(fn(Category $model) => $this->hydrateWithChildren($model))->values()->toArray();
    }

    public function getById(int $id): CategoryEntity
    {
        $model = Category::findOrFail($id);
        return $this->hydrateWithChildren($model);
    }

    public function save(CategoryEntity $category): CategoryEntity
    {
        $model = $category->id
            ? Category::findOrFail($category->id)
            : new Category();

        $model->name = $category->name;
        $model->slug = (string) $category->slug;
        $model->svg = $category->svgIcon;
        $model->published = $category->isPublished();
        $model->meta = $category->meta ? [
            'title' => $category->meta->getTitle(),
            'description' => $category->meta->getDescription(),
        ] : [];

        if ($category->parentId !== null) {
            $model->parent_id = $category->parentId;
        }

        $model->save();

        return $this->hydrate($model);
    }

    public function delete(int $id): void
    {
        $model = Category::findOrFail($id);
        $model->delete();
    }

    /**
     * @return CategoryEntity[]
     */
    public function getTree(): array
    {
        $roots = Category::with(['image', 'icon'])
            ->defaultOrder()
            ->withDepth()
            ->get()
            ->toTree();

        return $roots->map(fn(Category $model) => $this->hydrateWithChildren($model))->toArray();
    }

    public function existsSlug(string $slug, ?int $excludeId = null): bool
    {
        $query = Category::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }

    public function moveUp(int $id): void
    {
        $model = Category::findOrFail($id);
        $model->up();
    }

    public function moveDown(int $id): void
    {
        $model = Category::findOrFail($id);
        $model->down();
    }

    /**
     * @return int[]
     */
    public function getDescendantIds(int $id): array
    {
        $model = Category::findOrFail($id);
        return Category::where('_lft', '>', $model->_lft)
            ->where('_rgt', '<', $model->_rgt)
            ->pluck('id')
            ->toArray();
    }

    public function bulkTogglePublished(array $ids, bool $published): void
    {
        if (empty($ids)) {
            return;
        }
        Category::whereIn('id', $ids)->update(['published' => $published]);
    }

    public function hasChildren(int $id): bool
    {
        return Category::where('parent_id', $id)->exists();
    }

    /**
     * Преобразует Eloquent модель в Domain Entity.
     * Ссылки на изображения формируются через полиморфные связи Photo (трейты ImageField/IconField).
     */
    private function hydrate(Category $model): CategoryEntity
    {
        $entity = new CategoryEntity(
            name: $model->name,
            slug: new Slug($model->slug),
            parentId: $model->parent_id,
        );

        $entity->id = $model->id;

        $entity->svgIcon = $model->svg;
        $entity->published = $model->published;

        // Meta
        $metaData = is_array($model->meta) ? $model->meta : [];
        $entity->meta = new Meta(
            title: $metaData['title'] ?? '',
            description: $metaData['description'] ?? '',
        );

        // Nested Set поля
        $entity->left = $model->_lft ?? 0;
        $entity->right = $model->_rgt ?? 0;
        $entity->depth = $model->depth ?? 0;

        return $entity;
    }

    /**
     * Гидратация с рекурсивным заполнением children.
     */
    private function hydrateWithChildren(Category $model): CategoryEntity
    {
        $entity = $this->hydrate($model);

        if ($model->children !== null && $model->children->isNotEmpty()) {
            $entity->children = array_map(
                fn(Category $child) => $this->hydrateWithChildren($child),
                $model->children->all()
            );
        }

        return $entity;
    }
}
