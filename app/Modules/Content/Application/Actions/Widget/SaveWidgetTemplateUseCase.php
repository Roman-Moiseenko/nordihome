<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Widget;

use App\Modules\Content\Application\DTOs\Widget\WidgetContentUpdateData;
use App\Modules\Content\Application\Interfaces\WidgetRepositoryInterface;
use App\Modules\Content\Infrastructure\Services\WidgetFileService;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class SaveWidgetTemplateUseCase
{
    public function __construct(
        private WidgetRepositoryInterface $widgetRepository,
        private WidgetFileService $widgetFileService,
    )
    {
    }

    public function execute(int $id, WidgetContentUpdateData $dto, UserPermission $userPermission): void
    {
        if (!$userPermission->can('content.widget.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $widget = $this->widgetRepository->getById($id);

        $this->widgetFileService->saveContent(
            (string) $widget->category,
            $widget->slug,
            $dto->content,
        );
    }
}
