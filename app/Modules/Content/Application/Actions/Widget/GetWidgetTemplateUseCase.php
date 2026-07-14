<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Widget;

use App\Modules\Content\Application\Interfaces\WidgetRepositoryInterface;
use App\Modules\Content\Infrastructure\Services\WidgetFileService;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class GetWidgetTemplateUseCase
{
    public function __construct(
        private WidgetRepositoryInterface $widgetRepository,
        private WidgetFileService $widgetFileService,
    )
    {
    }

    public function execute(int $id, UserPermission $userPermission): string
    {
        if (!$userPermission->can('content.widget.view')) {
            throw new \DomainException('Доступ запрещён');
        }

        $widget = $this->widgetRepository->getById($id);
        return $this->widgetFileService->getContent(
            (string) $widget->category,
            $widget->slug,
        );
    }
}
