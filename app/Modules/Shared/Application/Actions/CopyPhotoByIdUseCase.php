<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Actions;

use App\Modules\Shared\Application\DTOs\JobPhotoCopyData;
use App\Modules\Shared\Application\DTOs\Photo\PhotoUploadData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Infrastructure\Job\RemoveTempPhotoJob;
use App\Modules\Shared\Infrastructure\Models\Photo;
use Illuminate\Http\UploadedFile;
use function now;

class CopyPhotoByIdUseCase
{
    public function __construct(
        private UploadPhotoUseCase $uploadPhotoUseCase,
    )
    {
    }

    public function execute(JobPhotoCopyData $dto, UserPermission $userPermission)
    {
        $uploadedFile = $this->downloadFile($dto->copyId);

        if ($uploadedFile === null) {
            return null;
        }

        $uploadDto = new PhotoUploadData(
            imageableId: $dto->imageableId,
            modelType: $dto->modelType,
            type: $dto->type,
            file: $uploadedFile,
        );

        return $this->uploadPhotoUseCase->execute($uploadDto, $userPermission);
    }

    private function downloadFile(int $copyId): ?UploadedFile
    {
        $photo = Photo::find($copyId);

        if ($photo === null) {
            \Log::error('Фото с ID ' . $copyId . ' не найдено для копирования');
            return null;
        }

        $sourcePath = $photo->getUploadFile();

        if (empty($sourcePath) || !is_file($sourcePath)) {
            \Log::error('Исходный файл не найден: ' . $sourcePath . ' для фото ID ' . $copyId);
            return null;
        }

        $storage = public_path() . '/temp/';
        $uploadFileName = $photo->file;
        $ext = pathinfo($uploadFileName, PATHINFO_EXTENSION);
        if (empty($ext)) {
            $ext = 'webp';
        }
        $fullFilename = $storage . uniqid() . '.' . $ext;

        if (!copy($sourcePath, $fullFilename)) {
            \Log::error('Не удалось скопировать файл из ' . $sourcePath . ' в ' . $fullFilename);
            return null;
        }

        RemoveTempPhotoJob::dispatch($fullFilename)->delay(now()->addMinutes(2));

        return new UploadedFile(
            $fullFilename,
            $uploadFileName,
            null,
            null,
            true,
        );
    }
}
