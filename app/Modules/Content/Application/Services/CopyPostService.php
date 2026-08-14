<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Services;

use App\Modules\Content\Application\Interfaces\ContentBlockRepositoryInterface;
use App\Modules\Content\Application\Interfaces\PostRepositoryInterface;
use App\Modules\Content\Application\Interfaces\WidgetInstanceRepositoryInterface;
use App\Modules\Content\Application\Interfaces\WidgetRepositoryInterface;
use App\Modules\Content\Domain\Entities\ContentBlockEntity;
use App\Modules\Content\Domain\Entities\PostEntity;
use App\Modules\Content\Domain\Entities\WidgetInstanceEntity;
use App\Modules\Content\Domain\ValueObjects\ContainerType;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\ValueObjects\Slug;

class CopyPostService
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private ContentBlockRepositoryInterface $contentBlockRepository,
        private WidgetInstanceRepositoryInterface $widgetInstanceRepository,
        private WidgetRepositoryInterface $widgetRepository,
    ) {}

    /**
     * Полностью копирует запись (post) вместе с блоками контента и
     * экземплярами виджетов, включая вложенные экземпляры.
     */
    public function execute(int $id, UserPermission $userPermission): PostEntity
    {
        if (!$userPermission->can('content.post.create')) {
            throw new \DomainException('Доступ запрещён');
        }

        $postEntity = $this->postRepository->getById($id);

        // 1. Копия записи с изменёнными name/slug/published
        $slug = new Slug((string) $postEntity->slug . '-copy');
        if ($this->postRepository->existsSlug((string) $slug)) {
            $slug = new Slug((string) $slug . '-' . uniqid());
        }

        $newPost = new PostEntity(
            name: $postEntity->name . ' (copy)',
            slug: $slug,
            categoryId: $postEntity->categoryId,
        );

        $newPost->caption = $postEntity->caption;
        $newPost->text = $postEntity->text;
        $newPost->fragment = $postEntity->fragment;
        $newPost->meta = $postEntity->meta;
        $newPost->oldRender = $postEntity->oldRender;
        $newPost->published = false;

        $newPost = $this->postRepository->save($newPost);

        // 2. Копии блоков контента с экземплярами виджетов
        $blocks = $this->contentBlockRepository->getByContainer(ContainerType::POST, $id);

        // old instance id => новый скопированный экземпляр
        $copiedInstances = [];

        foreach ($blocks as $block) {
            $newBlock = new ContentBlockEntity(
                containerType: ContainerType::post(),
                containerId: $newPost->id,
            );

            $newBlock->caption = $block->caption;
            $newBlock->section = $block->section;
            $newBlock->sort = $block->sort;
            $newBlock->active = $block->active;

            $sourceInstanceId = $block->widgetInstanceId
                ?? $block->widgetInstance?->id
                ?? null;

            if ($sourceInstanceId !== null) {
                $copiedInstance = $this->copyWidgetInstanceDeep($sourceInstanceId, $copiedInstances);
                $newBlock->widgetInstanceId = $copiedInstance->id;
                $newBlock->widgetInstance = $copiedInstance;
            }

            $this->contentBlockRepository->save($newBlock);
        }

        return $newPost;
    }

    /**
     * Рекурсивно копирует экземпляр виджета и все вложенные экземпляры,
     * на которые он ссылается через поля формата "widget".
     *
     * @param int $instanceId ID исходного экземпляра
     * @param array<int, WidgetInstanceEntity> $copiedInstances соответствие старых ID новым
     */
    private function copyWidgetInstanceDeep(int $instanceId, array &$copiedInstances): WidgetInstanceEntity
    {
        if (isset($copiedInstances[$instanceId])) {
            return $copiedInstances[$instanceId];
        }

        $source = $this->widgetInstanceRepository->getById($instanceId);

        // Создаём копию с исходными params, чтобы получить её id,
        // а затем (регистрация до рекурсии) разрываем возможные циклы.
        $copy = new WidgetInstanceEntity(
            widgetId: $source->widgetId,
            params: $source->params,
            title: $source->title,
        );

        $copy = $this->widgetInstanceRepository->save($copy);

        // Регистрируем копию до обработки вложенных ссылок.
        $copiedInstances[$instanceId] = $copy;

        // Заменяем ссылки на вложенные экземпляры на их копии.
        $copy->params = $this->copyParamsWithNestedInstances(
            $source->params,
            $source->widgetId,
            $copiedInstances,
        );

        $copy = $this->widgetInstanceRepository->save($copy);

        return $copy;
    }

    /**
     * Проходит по params согласно JSON Schema виджета и заменяет
     * идентификаторы вложенных экземпляров на идентификаторы их копий.
     */
    private function copyParamsWithNestedInstances(
        array $params,
        int $widgetId,
        array &$copiedInstances,
    ): array {
        $widget = $this->widgetRepository->getById($widgetId);
        $schema = $widget->schema->toArray();

        return $this->copyParamsRecursive($params, $schema['properties'] ?? [], $copiedInstances);
    }

    /**
     * Рекурсивный обход params в соответствии со схемой (объекты, массивы объектов,
     * поля формата "widget").
     */
    private function copyParamsRecursive(array $params, array $properties, array &$copiedInstances): array
    {
        $result = $params;

        foreach ($properties as $name => $prop) {
            if (!array_key_exists($name, $result)) {
                continue;
            }

            $format = $prop['format'] ?? null;
            $type = $prop['type'] ?? null;

            // Поле — ссылка на вложенный экземпляр виджета
            if ($format === 'widget') {
                $result[$name] = $this->copyWidgetReference($result[$name], $copiedInstances);
                continue;
            }

            // Вложенный объект
            if ($type === 'object' && isset($prop['properties']) && is_array($result[$name])) {
                $result[$name] = $this->copyParamsRecursive($result[$name], $prop['properties'], $copiedInstances);
                continue;
            }

            // Массив объектов
            if ($type === 'array'
                && isset($prop['items']['type'])
                && $prop['items']['type'] === 'object'
                && isset($prop['items']['properties'])
                && is_array($result[$name])
            ) {
                foreach ($result[$name] as $i => $item) {
                    if (is_array($item)) {
                        $result[$name][$i] = $this->copyParamsRecursive($item, $prop['items']['properties'], $copiedInstances);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Копирует один вложенный экземпляр и возвращает ссылку в исходной форме
     * (int, числовая строка или массив с ключом id).
     */
    private function copyWidgetReference(mixed $value, array &$copiedInstances): mixed
    {
        $instanceId = $this->resolveWidgetReferenceId($value);

        if ($instanceId === null) {
            return $value;
        }

        $copy = $this->copyWidgetInstanceDeep($instanceId, $copiedInstances);

        if (is_int($value)) {
            return $copy->id;
        }

        if (is_string($value)) {
            return (string) $copy->id;
        }

        // Массив вида {id, ...} — обновляем только идентификатор,
        // остальное (title, widgetName, ...) переобогатится при чтении формы.
        $value['id'] = $copy->id;

        return $value;
    }

    /**
     * Извлекает id вложенного экземпляра из значения поля формата "widget".
     */
    private function resolveWidgetReferenceId(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (int) $value;
        }

        if (is_array($value) && isset($value['id']) && is_numeric($value['id'])) {
            return (int) $value['id'];
        }

        return null;
    }
}
