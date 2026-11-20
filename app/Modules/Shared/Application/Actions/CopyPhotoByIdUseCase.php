<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Actions;

use App\Modules\Shared\Application\DTOs\JobPhotoCopyData;
use App\Modules\Shared\Application\DTOs\Photo\PhotoUploadData;
use App\Modules\Shared\Infrastructure\Job\RemoveTempPhotoJob;
use App\Modules\Shared\Infrastructure\Models\Photo;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use Illuminate\Http\UploadedFile;
use function now;

class CopyPhotoByIdUseCase
{
    public function __construct(
        private UploadPhotoUseCase $uploadPhotoUseCase,
        private PhotoService $photoService,
    )
    {
    }

    public function execute(JobPhotoCopyData $dto, bool $withThumbs = true)
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

        return $this->uploadPhotoUseCase->execute($uploadDto, $withThumbs);
    }

    private function downloadFile(int $copyId): ?UploadedFile
    {
        $photo = Photo::find($copyId);

        if ($photo === null) {
            \Log::error('Фото с ID ' . $copyId . ' не найдено для копирования');
            return null;
        }

        if (empty($photo->file)) {
            \Log::error('У фото ID ' . $copyId . ' не заполнено имя файла');
            return null;
        }

        // Получаем локальную копию через текущий драйвер хранилища (local/s3).
        // Для local это абсолютный путь к исходнику, для s3 — временный файл.
        $sourcePath = $this->photoService->localCopy(
            $photo->model_type,
            $photo->imageable_id,
            $photo->file,
        );

        if ($sourcePath === null) {
            \Log::error('Исходный файл не найден: ' . $photo->file . ' для фото ID ' . $copyId);
            return null;
        }

        // Для S3 localCopy возвращает временный файл — запланируем его удаление.
        if (str_starts_with($sourcePath, sys_get_temp_dir())) {
            RemoveTempPhotoJob::dispatch($sourcePath)->delay(now()->addMinutes(2));
        }

        return new UploadedFile(
            $sourcePath,
            $photo->file,
            null,
            null,
            true,
        );
    }
}
