<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\ContentBlock;

use App\Modules\Content\Domain\Entities\ContentBlockEntity;
use App\Modules\Content\Domain\Entities\WidgetInstanceEntity;
use App\Modules\Content\Domain\Interfaces\ContentBlockRepositoryInterface;
use App\Modules\Content\Domain\Interfaces\WidgetInstanceRepositoryInterface;
use App\Modules\Content\Domain\Interfaces\WidgetRepositoryInterface;

final readonly class CopyContentBlockUseCase
{
    public function __construct(
        private ContentBlockRepositoryInterface $contentBlockRepository,
        private WidgetInstanceRepositoryInterface $widgetInstanceRepository,
        private WidgetRepositoryInterface $widgetRepository,
    ) {}

    /**
     * Копирует блок контента вместе с экземпляром виджета (и вложенными
     * экземплярами виджетов) и вставляет копию сразу после исходного блока.
     */
    public function execute(int $id): ContentBlockEntity
    {
        $source = $this->contentBlockRepository->getById($id);

        $copiedInstances = [];
        $copiedInstance = null;

        $sourceInstanceId = $source->widgetInstanceId
            ?? $source->widgetInstance?->id
            ?? null;

        if ($sourceInstanceId !== null) {
            $copiedInstance = $this->copyWidgetInstanceDeep($sourceInstanceId, $copiedInstances);
        }

        $copy = new ContentBlockEntity(
            containerType: $source->containerType,
            containerId: $source->containerId,
        );

        $copy->caption = $source->caption;
        $copy->section = $source->section;
        $copy->active = $source->active;
        $copy->widgetInstanceId = $copiedInstance?->id;
        $copy->widgetInstance = $copiedInstance;

        $position = ($source->sort ?? 0) + 1;

        return $this->contentBlockRepository->insertAt($copy, $position);
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

        return $this->widgetInstanceRepository->save($copy);
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
        try {
            $widget = $this->widgetRepository->getById($widgetId);
            $schema = $widget->schema->toArray();
        } catch (\Throwable) {
            return $params;
        }

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
