<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Actions;

use App\Modules\Shared\Application\Interfaces\PhotoRepositoryInterface;
use App\Modules\Shared\Domain\Entities\PhotoEntity;
use App\Modules\Shared\Domain\ValueObjects\PhotoType;
use App\Modules\Shared\Infrastructure\Services\PhotoService;

readonly class GetGalleryThumbUseCase
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
    public function execute(int $imageableId, string $modelType, ?string $thumb = null): array
    {
        // Без проверки прав доступа
        $photos = $this->photoRepository->findAllByEntity(
            $imageableId,
            $modelType,
            new PhotoType(PhotoType::GALLERY),
        );
        if (empty($photos)) return [];

        return array_map(function (PhotoEntity $photo) use ($thumb) {
            if ($thumb === null)
                return $this->photoService->getUploadUrl(
                    $photo->modelType,
                    $photo->imageableId,
                    $photo->file
                );
            //TODO проверка, если thumb нет, то пересоздать
            return $this->photoService->getThumbUrl(
                $photo->id,
                $photo->modelType,
                $photo->imageableId,
                $photo->file,
                $thumb,
            );
        }, $photos);

    }
}
