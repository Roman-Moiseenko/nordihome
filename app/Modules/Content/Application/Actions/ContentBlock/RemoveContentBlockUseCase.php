<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\ContentBlock;

use App\Modules\Content\Application\Interfaces\ContentBlockRepositoryInterface;
use App\Modules\Content\Application\Interfaces\WidgetInstanceRepositoryInterface;

final readonly class RemoveContentBlockUseCase
{
    public function __construct(
        private ContentBlockRepositoryInterface $contentBlockRepository,
        private WidgetInstanceRepositoryInterface $widgetInstanceRepository,
    ) {}

    public function execute(int $id): void
    {
        $block = $this->contentBlockRepository->getById($id);

        // Удаляем связанные WidgetInstance
        if ($block->widgetInstance !== null && $block->widgetInstance->id !== null) {
            $this->widgetInstanceRepository->delete($block->widgetInstance->id);
        }

        // Удаляем сам ContentBlock
        $this->contentBlockRepository->delete($id);
    }
}
