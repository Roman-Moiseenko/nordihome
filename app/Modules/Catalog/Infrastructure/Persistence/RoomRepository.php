<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\Interfaces\RoomRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\RoomEntity;
use App\Modules\Catalog\Infrastructure\Models\Room;
use App\Modules\Shared\Domain\ValueObjects\Image;
use App\Modules\Shared\Domain\ValueObjects\Meta;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoomRepository implements RoomRepositoryInterface
{
    /**
     * @return RoomEntity[]
     */
    public function getAll(): array
    {
        $models = Room::defaultOrder()->withDepth()->get()->toTree();
        return $models->map(fn(Room $model) => $this->hydrateWithChildren($model))->values()->toArray();
    }

    public function getById(int $id): RoomEntity
    {
        $model = Room::findOrFail($id);
        return $this->hydrateWithChildren($model);
    }

    public function save(RoomEntity $room): RoomEntity
    {
        $model = $room->id
            ? Room::findOrFail($room->id)
            : new Room();

        $model->name = $room->name;
        $model->slug = (string) $room->slug;
        $model->svg = $room->svgIcon;
        $model->published = $room->isPublished();
        $model->meta = $room->meta ? [
            'title' => $room->meta->getTitle(),
            'description' => $room->meta->getDescription(),
        ] : [];

        if ($room->parentId !== null) {
            $model->parent_id = $room->parentId;
        }
        $model->wp_id = $room->wpId;
        $model->save();

        return $this->hydrate($model);
    }

    public function delete(int $id): void
    {
        $model = Room::findOrFail($id);
        $model->delete();
    }

    /**
     * @return RoomEntity[]
     */
    public function getTree(): array
    {
        $roots = Room::with(['image', 'icon'])
            ->defaultOrder()
            ->withDepth()
            ->get()
            ->toTree();

        return $roots->map(fn(Room $model) => $this->hydrateWithChildren($model))->toArray();
    }

    public function existsSlug(string $slug, ?int $excludeId = null): bool
    {
        $query = Room::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }
    public function existsByWpId(int $wpId): bool
    {
        return Room::where('wp_id', $wpId)->exists();
    }
    public function findByWpId(int $wpId): ?RoomEntity
    {
        $model = Room::where('wp_id', $wpId)->first();
        if ($model === null) return null;

        return $this->hydrate($model);
    }

    public function moveUp(int $id): void
    {
        $model = Room::findOrFail($id);
        $model->up();
    }

    public function moveDown(int $id): void
    {
        $model = Room::findOrFail($id);
        $model->down();
    }
    /**
     * @return int[]
     */
    public function getDescendantIds(int $id): array
    {
        $model = Room::findOrFail($id);
        return Room::where('_lft', '>', $model->_lft)
            ->where('_rgt', '<', $model->_rgt)
            ->pluck('id')
            ->toArray();
    }

    public function bulkTogglePublished(array $ids, bool $published): void
    {
        if (empty($ids)) {
            return;
        }
        Room::whereIn('id', $ids)->update(['published' => $published]);
    }

    public function hasChildren(int $id): bool
    {
        return Room::where('parent_id', $id)->exists();
    }
    /**
     * Преобразует Eloquent модель в Domain Entity.
     * Ссылки на изображения формируются через полиморфные связи Photo (трейты ImageField/IconField).
     */
    private function hydrate(Room $model): RoomEntity
    {
        $entity = new RoomEntity(
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
        $entity->wpId = $model->wp_id;
        // Nested Set поля
        $entity->left = $model->_lft ?? 0;
        $entity->right = $model->_rgt ?? 0;
        $entity->depth = $model->depth ?? 0;
        return $entity;
    }

    /**
     * Гидратация с рекурсивным заполнением children.
     */
    private function hydrateWithChildren(Room $model): RoomEntity
    {
        $entity = $this->hydrate($model);

        if ($model->children !== null && $model->children->isNotEmpty()) {
            $entity->children = array_map(
                fn(Room $child) => $this->hydrateWithChildren($child),
                $model->children->all()
            );
        }

        return $entity;
    }
}
