<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Actions;

use App\Modules\Shared\Application\DTOs\Photo\PhotoSaveData;
use App\Modules\Shared\Application\Interfaces\PhotoRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class SavePhotoDataUseCase
{
    public function __construct(
        private PhotoRepositoryInterface $photoRepository,
    )
    {
    }

    public function execute(int $id, PhotoSaveData $dto, UserPermission $userPermission): void
    {
        // Проверка прав доступа
        // if (!$userPermission->can('storage.photo.update')) throw new \DomainException('Доступ запрещён');

        $data = array_filter([
            'alt' => $dto->alt,
            'title' => $dto->title,
            'description' => $dto->description,
        ], fn($value) => $value !== null);

        $this->photoRepository->update($id, $data);
    }
}
