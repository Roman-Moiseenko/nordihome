<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Widget;

use App\Modules\Content\Domain\Interfaces\WidgetRepositoryInterface;
use App\Modules\Content\Infrastructure\Services\WidgetFileService;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class RemoveWidgetUseCase
{
    public function __construct(
        private WidgetRepositoryInterface $widgetRepository,
        private WidgetFileService $widgetFileService,
    )
    {
    }

    public function execute(int $id, UserPermission $userPermission): void
    {
        if (!$userPermission->can('content.widget.delete')) {
            throw new \DomainException('Доступ запрещён');
        }

        $widget = $this->widgetRepository->getById($id);

        $this->widgetRepository->delete($id);

        $this->widgetFileService->deleteTemplateFile((string) $widget->category, $widget->slug);
    }
}
