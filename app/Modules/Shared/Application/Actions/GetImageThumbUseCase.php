<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Actions;

use App\Modules\Shared\Domain\Interfaces\PhotoRepositoryInterface;
use App\Modules\Shared\Domain\ValueObjects\PhotoType;
use App\Modules\Shared\Infrastructure\Services\PhotoService;

readonly class GetImageThumbUseCase
{
    public function __construct(
        private PhotoRepositoryInterface $photoRepository,
        private PhotoService             $photoService,
    )
    {
    }

    /**
     * Возвращает url изображения.
     * Если thumb == null — возвращает url оригинального файла (getUploadUrl).
     * Если thumb задан — возвращает url копии (getThumbUrl).
     */
    public function execute(int $imageableId, string $modelType, ?string $thumb = null): string
    {
        // Без проверки прав доступа
        $photo = $this->photoRepository->findByEntity(
            $imageableId,
            $modelType,
            new PhotoType(PhotoType::IMAGE),
        );
        if ($photo === null) return '';

        // Если thumb не передан — возвращаем url оригинального файла
        if ($thumb === null)
            return $this->photoService->getUploadUrl(
                $photo->modelType,
                $photo->imageableId,
                $photo->file
            );
        //TODO проверка, если thumb нет, то пересоздать

        // Возвращаем url thumb (копии)
        return $this->photoService->getThumbUrl(
            $photo->id,
            $photo->modelType,
            $photo->imageableId,
            $photo->file,
            $thumb,
        );
    }
}
