<?php

declare(strict_types=1);

namespace App\Modules\Content\Infrastructure\Persistence;

use App\Modules\Content\Application\Interfaces\WidgetInstanceRepositoryInterface;
use App\Modules\Content\Domain\Entities\WidgetInstanceEntity;
use App\Modules\Content\Infrastructure\Models\WidgetInstance;
use DateTimeImmutable;

class WidgetInstanceRepository implements WidgetInstanceRepositoryInterface
{
    /** @return WidgetInstanceEntity[] */
    public function getAll(): array
    {
        return WidgetInstance::with('widget')
            ->get()
            ->map(fn(WidgetInstance $model) => $this->hydrate($model))
            ->toArray();
    }

    public function getById(int $id): WidgetInstanceEntity
    {
        $model = WidgetInstance::with('widget')->findOrFail($id);
        return $this->hydrate($model);
    }

    public function save(WidgetInstanceEntity $widgetInstance): WidgetInstanceEntity
    {
        $model = $widgetInstance->id !== null
            ? WidgetInstance::findOrFail($widgetInstance->id)
            : new WidgetInstance();

        $model->widget_id = $widgetInstance->widgetId;
        $model->params = $widgetInstance->params;
        $model->title = $widgetInstance->title;

        $model->save();

        return $this->hydrate($model->fresh()->load('widget'));
    }

    public function delete(int $id): void
    {
        $model = WidgetInstance::findOrFail($id);
        $model->delete();
    }

    /** @return WidgetInstanceEntity[] */
    public function getByWidgetId(int $widgetId): array
    {
        return WidgetInstance::with('widget')
            ->where('widget_id', $widgetId)
            ->get()
            ->map(fn(WidgetInstance $model) => $this->hydrate($model))
            ->toArray();
    }

    private function hydrate(WidgetInstance $model): WidgetInstanceEntity
    {
        $entity = new WidgetInstanceEntity(
            widgetId: $model->widget_id,
            params: $model->params ?? [],
            title: $model->title,
        );

        $entity->id = $model->id;

        if ($model->widget !== null) {
            $entity->widgetName = $model->widget->name;
            $entity->widgetSlug = $model->widget->slug;
        }

        if ($model->created_at !== null) {
            $entity->createdAt = new DateTimeImmutable($model->created_at->toDateTimeString());
        }
        if ($model->updated_at !== null) {
            $entity->updatedAt = new DateTimeImmutable($model->updated_at->toDateTimeString());
        }

        return $entity;
    }
}
