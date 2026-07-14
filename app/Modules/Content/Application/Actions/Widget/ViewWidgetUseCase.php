<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Widget;

use App\Modules\Content\Application\Interfaces\WidgetRepositoryInterface;
use App\Modules\Content\Domain\Entities\WidgetEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class ViewWidgetUseCase
{
    public function __construct(
        private WidgetRepositoryInterface $widgetRepository,
    )
    {
    }

    public function execute(int $id, UserPermission $userPermission): WidgetEntity
    {
        if (!$userPermission->can('content.widget.view')) {
            throw new \DomainException('Доступ запрещён');
        }

        return $this->widgetRepository->getById($id);
    }
}
