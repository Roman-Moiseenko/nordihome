<?php

namespace App\Modules\Shared\Application\Actions;

use App\Modules\Shared\Application\DTOs\JobPhotoCopyData;
use App\Modules\Shared\Application\DTOs\Photo\PhotoUploadData;
use App\Modules\Shared\Domain\Entities\UserPermission;

class CopyPhotoByIdUseCase
{
    public function __construct(
        private UploadPhotoUseCase $uploadPhotoUseCase,
    )
    {
    }

    public function execute(JobPhotoCopyData $dto, UserPermission   $userPermission)
    {
        $uploadedFile = $this->downloadFile($dto->copyId);

        $uploadDto = new PhotoUploadData(
            imageableId: $dto->imageableId,
            modelType: $dto->modelType,
            type: $dto->type,
            file: $uploadedFile,
        );

        return $this->uploadPhotoUseCase->execute($uploadDto, $userPermission);
    }

    private function downloadFile(int $copyId)
    {

    }


}
