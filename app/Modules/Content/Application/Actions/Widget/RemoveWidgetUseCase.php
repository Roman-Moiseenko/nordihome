<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Widget;

use App\Modules\Content\Application\Interfaces\WidgetRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class RemoveWidgetUseCase
{
    public function __construct(
        private WidgetRepositoryInterface $widgetRepository,
    )
    {
    }

    public function execute(int $id, UserPermission $userPermission): void
    {
        if (!$userPermission->can('content.widget.delete')) {
            throw new \DomainException('Доступ запрещён');
        }

        $this->widgetRepository->delete($id);
    }
}
