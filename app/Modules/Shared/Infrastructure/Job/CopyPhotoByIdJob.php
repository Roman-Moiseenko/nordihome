<?php

namespace App\Modules\Shared\Infrastructure\Job;

use App\Modules\Shared\Application\Actions\CopyPhotoByIdUseCase;
use App\Modules\Shared\Application\Actions\SavePhotoDataUseCase;
use App\Modules\Shared\Application\DTOs\JobPhotoCopyData;
use App\Modules\Shared\Application\DTOs\Photo\PhotoSaveData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CopyPhotoByIdJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct(
        private readonly JobPhotoCopyData $dto,
        private readonly UserPermission   $userPermission,
    )
    {
    }

    public function handle(
        SavePhotoDataUseCase    $savePhotoDataUseCase,
        CopyPhotoByIdUseCase $copyPhotoByIdUseCase
    ): void
    {
        $photo = $copyPhotoByIdUseCase->execute($this->dto, $this->userPermission);

        if ($photo !== null && $this->dto->alt !== null) {
            $saveDto = new PhotoSaveData(alt: $this->dto->alt,);
            $savePhotoDataUseCase->execute($photo->id, $saveDto, $this->userPermission);
        }

    }
}
