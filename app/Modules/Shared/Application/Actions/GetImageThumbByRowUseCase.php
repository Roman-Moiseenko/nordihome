<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Actions;

use App\Modules\Shared\Infrastructure\Services\PhotoService;

readonly class GetImageThumbByRowUseCase
{
    public function __construct(
        private PhotoService             $photoService,
    )
    {
    }

    /**
     * Возвращает url изображения.
     * Если thumb == null — возвращает url оригинального файла (getUploadUrl).
     * Если thumb задан — возвращает url копии (getThumbUrl).
     * id, photo_id, photo_file, model_type
     */
    public function execute(\stdClass $row, ?string $thumb = null): string
    {
        if (empty($row->photo_file) || empty($row->photo_id)) {
            return '/images/no-image.jpg';
        }

        if ($thumb == null) {
            return $this->photoService->getUploadUrl(
                $row->model_type,
                $row->id,
                $row->photo_file
            );
        }
        return $this->photoService->getThumbUrl(
            photoId: (int) $row->photo_id,
            modelType: $row->model_type,
            imageableId: (int) $row->id,
            fileName: $row->photo_file,
            thumb: $thumb,
        );
    }
}
