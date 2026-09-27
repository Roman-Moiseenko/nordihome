<?php

namespace App\Modules\Shared\Application\Actions;

use App\Modules\Shared\Application\DTOs\Photo\PhotoThumbData;

class GetPhotoStatic
{

    public static function get($modelType, $id, $thumb = null)
    {
        $dto = new PhotoThumbData(
            imageableId: $id,
            modelType: $modelType,
            type: 'image',
            thumb: $thumb,
        );
        $useCase = app()->make(GetPhotoThumbUseCase::class);
        return $useCase->execute($dto);
    }

    public static function gallery($modelType, $id, $thumb = null)
    {
        $dto = new PhotoThumbData(
            imageableId: $id,
            modelType: $modelType,
            type: 'gallery',
            thumb: $thumb,
        );
        $useCase = app()->make(GetPhotoThumbUseCase::class);
        return $useCase->execute($dto);
    }
}
