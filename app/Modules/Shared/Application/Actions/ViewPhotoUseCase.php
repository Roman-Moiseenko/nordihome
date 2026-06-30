<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Actions;

use App\Modules\Shared\Application\Interfaces\PhotoRepositoryInterface;
use App\Modules\Shared\Domain\Entities\PhotoEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class ViewPhotoUseCase
{
    public function __construct(
        private PhotoRepositoryInterface $photoRepository,
    )
    {
    }

    public function execute(int $id, UserPermission $userPermission): PhotoEntity
    {
        // Без проверки прав доступа
        return $this->photoRepository->getById($id);
    }
}
