<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Actions;

use App\Modules\Shared\Application\DTOs\Photo\PhotoThumbData;
use App\Modules\Shared\Application\Interfaces\PhotoRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\ValueObjects\PhotoType;
use App\Modules\Shared\Infrastructure\Services\PhotoService;

readonly class GetPhotoThumbUseCase
{
    public function __construct(
        private PhotoRepositoryInterface $photoRepository,
        private PhotoService $photoService,
    )
    {
    }

    /**
     * Возвращает url изображения.
     * Если thumb == null — возвращает url оригинального файла (getUploadUrl).
     * Если thumb задан — возвращает url копии (getThumbUrl).
     */
    public function execute(PhotoThumbData $dto, UserPermission $userPermission): string
    {
        // Без проверки прав доступа

        $photo = $this->photoRepository->findByEntity(
            (int) $dto->imageableId,
            $dto->modelType,
            new PhotoType($dto->type),
        );

        if ($photo === null) {
            throw new \DomainException('Изображение не найдено');
        }

        // Если thumb не передан — возвращаем url оригинального файла
        if ($dto->thumb === null || $dto->thumb === '') {
            return $photo->uploadUrl;
        }

        // Возвращаем url thumb (копии)
        return $this->photoService->getThumbUrl(
            $photo->id,
            $photo->modelType,
            $photo->imageableId,
            $photo->file,
            $dto->thumb,
            $photo->thumb,
        );
    }
}
