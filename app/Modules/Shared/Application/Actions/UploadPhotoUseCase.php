<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Actions;

use App\Modules\Shared\Application\DTOs\Photo\PhotoUploadData;
use App\Modules\Shared\Domain\Entities\PhotoEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Interfaces\PhotoRepositoryInterface;
use App\Modules\Shared\Domain\ValueObjects\PhotoType;
use App\Modules\Shared\Infrastructure\Mappers\ModelTypeMapper;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use Illuminate\Http\UploadedFile;

readonly class UploadPhotoUseCase
{
    public function __construct(
        private PhotoRepositoryInterface $photoRepository,
        private PhotoService $photoService,
    )
    {
    }

    public function execute(PhotoUploadData $dto, UserPermission $userPermission): PhotoEntity
    {
        // Проверка прав доступа
        // if (!$userPermission->can('storage.photo.upload')) throw new \DomainException('Доступ запрещён');

        $fqcn = ModelTypeMapper::toFqcn($dto->modelType);

        /** @var UploadedFile|null $file */
        $file = $dto->file;
        $fileName = $file ? $this->photoService->uploadFile(
            $dto->modelType,
            $dto->imageableId,
            $file,
        ) : '';


        $photo = new PhotoEntity(
            imageableId: $dto->imageableId,
            imageableType: $fqcn,
            modelType: $dto->modelType,
            file: $fileName,
            type: new PhotoType($dto->type),
        );

        // Извлекаем метаданные изображения из файла
        if ($file) {
            $photo->format = $file->getMimeType();
            $size = @getimagesize($file->getPathname());
            if (is_array($size)) {
                $photo->width = (int)$size[0];
                $photo->height = (int)$size[1];
            }
        }

        return $this->photoRepository->save($photo);
    }
}
