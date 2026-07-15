<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\WidgetInstance;

use App\Modules\Content\Application\DTOs\WidgetInstance\WidgetInstanceCreateData;
use App\Modules\Content\Application\Interfaces\ContentBlockRepositoryInterface;
use App\Modules\Content\Application\Interfaces\WidgetInstanceRepositoryInterface;
use App\Modules\Content\Domain\Entities\WidgetInstanceEntity;

final readonly class CreateWidgetInstanceUseCase
{
    public function __construct(
        private WidgetInstanceRepositoryInterface $widgetInstanceRepository,
        private ContentBlockRepositoryInterface $contentBlockRepository,
    ) {}

    public function execute(WidgetInstanceCreateData $dto): WidgetInstanceEntity
    {
        // Создаём экземпляр виджета
        $instance = new WidgetInstanceEntity(
            widgetId: $dto->widget_id,
            params: $dto->params,
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
}
