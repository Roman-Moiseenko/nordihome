<?php

declare(strict_types=1);

namespace App\Modules\Shared\Infrastructure\Job;

use App\Modules\Shared\Application\Actions\SavePhotoDataUseCase;
use App\Modules\Shared\Application\Actions\UploadPhotoByUrlUseCase;
use App\Modules\Shared\Application\DTOs\JobPhotoLoadData;
use App\Modules\Shared\Application\DTOs\Photo\PhotoSaveData;
use App\Modules\Shared\Application\DTOs\Photo\PhotoUploadByUrlData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LoadPhotoByUrlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly JobPhotoLoadData $dto,
        private readonly UserPermission   $userPermission,
    )
    {
    }

    public function handle(
        SavePhotoDataUseCase    $savePhotoDataUseCase,
        UploadPhotoByUrlUseCase $uploadPhotoByUrlUseCase,
    ): void
    {
        $uploadDto = new PhotoUploadByUrlData(
            imageableId: $this->dto->imageableId,
            modelType: $this->dto->modelType,
            type: $this->dto->type,
            url: $this->dto->url,
        );

        $photo = $uploadPhotoByUrlUseCase->execute($uploadDto, $this->userPermission);

        if ($photo !== null && $this->dto->alt !== null) {
            $saveDto = new PhotoSaveData(alt: $this->dto->alt,);
            $savePhotoDataUseCase->execute($photo->id, $saveDto, $this->userPermission);
        }
    }
}
