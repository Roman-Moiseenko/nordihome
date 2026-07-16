<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\WidgetInstance;

use App\Modules\Content\Application\DTOs\WidgetInstance\WidgetInstanceCreateData;
use App\Modules\Content\Application\Interfaces\ContentBlockRepositoryInterface;
use App\Modules\Content\Application\Interfaces\WidgetInstanceRepositoryInterface;
use App\Modules\Content\Application\Interfaces\WidgetRepositoryInterface;
use App\Modules\Content\Domain\Entities\WidgetInstanceEntity;

final readonly class CreateWidgetInstanceUseCase
{
    public function __construct(
        private WidgetInstanceRepositoryInterface $widgetInstanceRepository,
        private ContentBlockRepositoryInterface $contentBlockRepository,
        private WidgetRepositoryInterface $widgetRepository,
    ) {}

    public function execute(WidgetInstanceCreateData $dto): WidgetInstanceEntity
    {
        // Если params не переданы — генерируем дефолтные из схемы виджета
        $params = $dto->params ?? $this->generateDefaultParams($dto->widget_id);

        // Создаём экземпляр виджета
        $instance = new WidgetInstanceEntity(
            widgetId: $dto->widget_id,
            params: $params,
            title: $dto->title,
        );

        $instance = $this->widgetInstanceRepository->save($instance);

        // Если указан content_block_id — привязываем к ContentBlock
        if ($dto->content_block_id !== null) {
            $block = $this->contentBlockRepository->getById($dto->content_block_id);
            $block->widgetInstanceId = $instance->id;
            $block->widgetInstance = $instance;
            $this->contentBlockRepository->save($block);
        }

        return $instance;
    }

    /**
     * Рекурсивно собирает дефолтные значения из JSON Schema.
     */
    private function generateDefaultParams(int $widgetId): array
    {
        $widget = $this->widgetRepository->getById($widgetId);
        $schema = $widget->schema->toArray();

        return $this->extractDefaults($schema);
    }

    private function extractDefaults(array $schemaNode): array
    {
        $result = [];

        $properties = $schemaNode['properties'] ?? [];

        foreach ($properties as $key => $prop) {
            // Если у свойства есть default — берём его
            if (array_key_exists('default', $prop)) {
                $result[$key] = $prop['default'];
                continue;
            }

            // Если это вложенный объект с properties — рекурсия
            if (isset($prop['type']) && $prop['type'] === 'object' && isset($prop['properties'])) {
                $result[$key] = $this->extractDefaults($prop);
                continue;
            }

            // Иначе — значение по умолчанию в зависимости от типа
            $result[$key] = match ($prop['type'] ?? 'string') {
                'string' => '',
                'integer', 'number' => 0,
                'boolean' => false,
                'array' => [],
                default => null,
            };
        }

        return $result;
    }
}
