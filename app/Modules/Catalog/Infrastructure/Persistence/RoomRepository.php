<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\Interfaces\RoomRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\RoomEntity;
use App\Modules\Catalog\Infrastructure\Models\Room;
use App\Modules\Shared\Domain\ValueObjects\Image;
use App\Modules\Shared\Domain\ValueObjects\Meta;
use App\Modules\Shared\Domain\ValueObjects\Slug;
class RoomRepository implements RoomRepositoryInterface
{
    /**
     * @return RoomEntity[]
     */
    public function getAll(): array
    {
        $models = Room::with(['image', 'icon'])->get();
        return $models->map(fn(Room $model) => $this->hydrate($model))->toArray();
    }

    public function getById(int $id): RoomEntity
    {
        $model = Room::with(['image', 'icon'])->findOrFail($id);
        return $this->hydrate($model);
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

        $model->save();

        return $this->hydrate($model->fresh()->load(['image', 'icon']));
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
            ->whereNull('parent_id')
            ->get();

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

        // Image — через полиморфную связь (трейт ImageField)
        if ($model->relationLoaded('image') && $model->image && $model->image->file) {
            $entity->image = new Image(
                url: $model->image->getUploadUrl(),
                alt: $model->image->alt,
            );
        }

        // Icon — через полиморфную связь (трейт IconField)
        if ($model->relationLoaded('icon') && $model->icon && $model->icon->file) {
            $entity->icon = new Image(
                url: $model->icon->getUploadUrl(),
                alt: $model->icon->alt,
            );
        }

        $entity->svgIcon = $model->svg;
        $entity->published = (bool) $model->published;

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
    private function hydrateWithChildren(Room $model): RoomEntity
    {
        $entity = $this->hydrate($model);

        if ($model->relationLoaded('children')) {
            $entity->children = array_map(
                fn(Room $child) => $this->hydrateWithChildren($child),
                $model->children->all()
            );
        }

        return $entity;
    }
}
