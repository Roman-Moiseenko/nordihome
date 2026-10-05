<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\DTOs\Group\FilterGroupIndexData;
use App\Modules\Catalog\Domain\Entities\GroupEntity;
use App\Modules\Catalog\Domain\Interfaces\GroupRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Models\Group;
use App\Modules\Shared\Domain\ValueObjects\Meta;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Pagination\LengthAwarePaginator;

class GroupRepository implements GroupRepositoryInterface
{
    public function getById(int $id): GroupEntity
    {
        return $this->hydrate(Group::withCount('products')->findOrFail($id));
    }

    /**
     * @return GroupEntity[]
     */
    public function findByProductId(int $productId): array
    {
        return Group::whereHas('products', function ($query) use ($productId) {
            $query->where('id', $productId);
        })
            ->withCount('products')
            ->orderBy('name')
            ->get()
            ->map(fn(Group $model) => $this->hydrate($model))
            ->toArray();
    }

    public function getAll(): array
    {
        return Group::orderBy('name')
            ->get()
            ->map(fn(Group $model) => $this->hydrate($model))
            ->toArray();
    }
    public function getAllPaginated(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return Group::withCount('products')
            ->orderBy('name')
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(fn(Group $model) => $this->hydrate($model));
    }

    public function save(GroupEntity $group): GroupEntity
    {
        $model = $group->id
            ? Group::findOrFail($group->id)
            : new Group();

        $model->name = $group->name;
        $model->slug = (string) $group->slug;
        $model->description = $group->description;
        $model->published = $group->isPublished();
        $model->meta = $group->meta ? [
            'title' => $group->meta->getTitle(),
            'description' => $group->meta->getDescription(),
        ] : [];

        $model->save();

        return $this->hydrate($model->fresh()->loadCount('products'));
    }

    public function delete(int $id): void
    {
        Group::findOrFail($id)->delete();
    }

    public function existsSlug(string $slug, ?int $excludeId = null): bool
    {
        $query = Group::where('slug', $slug);
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function filterPaginate(FilterGroupIndexData &$filter): LengthAwarePaginator
    {
        $query = Group::withCount('products')->orderByDesc('id');

        $filter->count = 0;

        if (!is_null($filter->name) && trim($filter->name) !== '') {
            $name = trim($filter->name);
            $query->where('name', 'like', "%{$name}%");
            $filter->count++;
        }

        if (!is_null($filter->product) && trim($filter->product) !== '') {
            $product = trim($filter->product);
            $query->whereHas('products', function ($query) use ($product) {
                $query->where('name', 'like', "%{$product}%")
                    ->orWhere('code', 'like', "%{$product}%")
                    ->orWhere('code_search', 'like', "%{$product}%");
            });
            $filter->count++;
        }

        return $query->paginate($filter->perPage)
            ->through(fn(Group $model) => $this->hydrate($model));
    }

    /**
     * Преобразует Eloquent модель в Domain Entity.
     */
    private function hydrate(Group $model): GroupEntity
    {
        $entity = new GroupEntity(
            name: $model->name,
            slug: new Slug($model->slug),
        );

        $entity->id = $model->id;
        $entity->description = (string) ($model->description ?? '');
        $entity->published = (bool) $model->published;

        $metaData = is_array($model->meta) ? $model->meta : [];
        $entity->meta = new Meta(
            title: $metaData['title'] ?? '',
            description: $metaData['description'] ?? '',
        );

        $entity->quantity = (int) ($model->products_count ?? 0);

        return $entity;
    }

}
