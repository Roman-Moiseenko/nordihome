<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Actions;

use App\Modules\Shared\Application\DTOs\Photo\PhotoSortData;
use App\Modules\Shared\Application\Interfaces\PhotoRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class SortPhotoUseCase
{
    public function __construct(
        private PhotoRepositoryInterface $photoRepository,
    )
    {
    }

    public function execute(int $id, PhotoSortData $dto, UserPermission $userPermission): void
    {
        // Проверка прав доступа
        // if (!$userPermission->can('storage.photo.update')) throw new \DomainException('Доступ запрещён');

        $this->photoRepository->update($id, ['sort' => $dto->sort]);
    }
}
