<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Actions;

use App\Modules\Shared\Application\Interfaces\PhotoRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class RemovePhotoUseCase
{
    public function __construct(
        private PhotoRepositoryInterface $photoRepository,
    )
    {
    }

    public function execute(int $id, UserPermission $userPermission): void
    {
        // Проверка прав доступа
        // if (!$userPermission->can('storage.photo.remove')) throw new \DomainException('Доступ запрещён');

        $this->photoRepository->delete($id);
    }
}
