<?php

declare(strict_types=1);

namespace App\Modules\Content\Infrastructure\Persistence;

use App\Modules\Content\Application\Interfaces\ContentBlockRepositoryInterface;
use App\Modules\Content\Domain\Entities\ContentBlockEntity;
use App\Modules\Content\Domain\ValueObjects\ContainerType;
use App\Modules\Content\Infrastructure\Models\ContentBlock;
use DateTimeImmutable;

class ContentBlockRepository implements ContentBlockRepositoryInterface
{
    /** @return ContentBlockEntity[] */
    public function getAll(): array
    {
        return ContentBlock::with(['widgetInstance.widget'])
            ->orderBy('sort_order')
            ->get()
            ->map(fn(ContentBlock $model) => $this->hydrate($model))
            ->toArray();
    }

    public function getById(int $id): ContentBlockEntity
    {
        $model = ContentBlock::with(['widgetInstance.widget'])->findOrFail($id);
        return $this->hydrate($model);
    }

    public function save(ContentBlockEntity $contentBlock): ContentBlockEntity
    {
        $model = $contentBlock->id !== null
            ? ContentBlock::findOrFail($contentBlock->id)
            : new ContentBlock();

        $model->container_type = (string) $contentBlock->containerType;
        $model->container_id = $contentBlock->containerId;
        $model->widget_instance_id = $contentBlock->widgetInstanceId ?: null;
        $model->sort_order = $contentBlock->sort;
        $model->section = $contentBlock->section;
        $model->caption = $contentBlock->caption;

        $model->save();

        return $this->hydrate($model->fresh()->load(['widgetInstance.widget']));
    }

    public function delete(int $id): void
    {
        $model = ContentBlock::findOrFail($id);
        $model->delete();
    }

    /** @return ContentBlockEntity[] */
    public function getByContainer(string $containerType, int $containerId): array
    {
        return ContentBlock::with(['widgetInstance.widget'])
            ->where('container_type', $containerType)
            ->where('container_id', $containerId)
            ->orderBy('sort_order')
            ->get()
            ->map(fn(ContentBlock $model) => $this->hydrate($model))
            ->toArray();
    }

    public function updateSortOrder(int $blockId, int $newSort): void
    {
        $block = ContentBlock::findOrFail($blockId);

        $currentSort = $block->sort_order;

        if ($currentSort === $newSort) {
            return;
        }

        $containerType = $block->container_type;
        $containerId = $block->container_id;

        // Получаем все блоки контейнера, исключая текущий
        $siblings = ContentBlock::where('container_type', $containerType)
            ->where('container_id', $containerId)
            ->where('id', '!=', $blockId)
            ->orderBy('sort_order')
            ->get();

        $block->sort_order = $newSort;
        $block->save();

        // Собираем все блоки с их новыми позициями
        $allBlocks = $siblings->toBase()->push($block->fresh())
            ->sortBy('sort_order')
            ->values();

        // Перенумеровываем
        foreach ($allBlocks as $index => $b) {
            if ($b->sort_order !== $index + 1) {
                ContentBlock::withoutTimestamps(function () use ($b, $index) {
                    $b->update(['sort_order' => $index + 1]);
                });
            }
        }
    }
    /**
     * Базовая гидратация одной сущности ContentBlock.
     * @throws \DateMalformedStringException
     */
    private function hydrate(ContentBlock $model): ContentBlockEntity
    {
        $entity = new ContentBlockEntity(
            containerType: new ContainerType($model->container_type),
            containerId: $model->container_id,
            widgetInstanceId: $model->widget_instance_id,
            sort: $model->sort_order,
            section: $model->section,
            caption: $model->caption,
        );
        $entity->active = (bool) $model->active;
        $entity->id = $model->id;

        // Гидратация связанного WidgetInstance
        if ($model->relationLoaded('widgetInstance') && $model->widgetInstance !== null) {
            $widgetInstance = $model->widgetInstance;

            $instanceEntity = new \App\Modules\Content\Domain\Entities\WidgetInstanceEntity(
                widgetId: $widgetInstance->widget_id,
                params: $widgetInstance->params ?? [],
                title: $widgetInstance->title,
            );

            $instanceEntity->id = $widgetInstance->id;

            if ($widgetInstance->relationLoaded('widget') && $widgetInstance->widget !== null) {
                $instanceEntity->widgetName = $widgetInstance->widget->name;
                $instanceEntity->widgetSlug = $widgetInstance->widget->slug;
            }

            if ($widgetInstance->created_at !== null) {
                $instanceEntity->createdAt = new DateTimeImmutable($widgetInstance->created_at->toDateTimeString());
            }
            if ($widgetInstance->updated_at !== null) {
                $instanceEntity->updatedAt = new DateTimeImmutable($widgetInstance->updated_at->toDateTimeString());
            }

            $entity->widgetInstance = $instanceEntity;
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
