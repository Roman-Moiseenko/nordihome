<?php

declare(strict_types=1);

namespace App\Modules\Content\Infrastructure\Persistence;

use App\Modules\Content\Application\Interfaces\WidgetRepositoryInterface;
use App\Modules\Content\Domain\Entities\WidgetEntity;
use App\Modules\Content\Domain\ValueObjects\WidgetCategory;
use App\Modules\Content\Domain\ValueObjects\WidgetSchema;
use App\Modules\Content\Infrastructure\Models\Widget;
use DateTimeImmutable;

class WidgetRepository implements WidgetRepositoryInterface
{
    /** @return WidgetEntity[] */
    public function getAll(): array
    {
        return Widget::all()
            ->map(fn(Widget $model) => $this->hydrate($model))
            ->toArray();
    }

    public function getById(int $id): WidgetEntity
    {
        $model = Widget::findOrFail($id);
        return $this->hydrate($model);
    }

    public function save(WidgetEntity $widget): WidgetEntity
    {
        $model = $widget->id !== null
            ? Widget::findOrFail($widget->id)
            : new Widget();

        $model->name = $widget->name;
        $model->slug = $widget->slug;
        $model->description = $widget->description;
        $model->category = (string) $widget->category;
        $model->schema = $widget->schema->toArray();

        $model->save();

        return $this->hydrate($model->fresh());
    }

    public function delete(int $id): void
    {
        $model = Widget::findOrFail($id);
        $model->delete();
    }

    public function existsSlug(string $slug, ?int $excludeId = null): bool
    {
        $query = Widget::where('slug', $slug);
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }

    private function hydrate(Widget $model): WidgetEntity
    {
        $entity = new WidgetEntity(
            name: $model->name,
            slug: $model->slug,
            category: new WidgetCategory($model->category),
            schema: WidgetSchema::fromArray($model->schema ?? []),
            description: $model->description,
        );

        $entity->id = $model->id;

        if ($model->created_at !== null) {
            $entity->createdAt = new DateTimeImmutable($model->created_at->toDateTimeString());
        }
        if ($model->updated_at !== null) {
            $entity->updatedAt = new DateTimeImmutable($model->updated_at->toDateTimeString());
        }

        return $entity;
    }
}
