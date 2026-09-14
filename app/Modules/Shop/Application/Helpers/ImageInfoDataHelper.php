<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Helpers;

use App\Modules\Shared\Application\Actions\GetImageThumbByRowUseCase;
use App\Modules\Shop\Application\DTOs\Elements\ImageInfoData;

readonly class ImageInfoDataHelper
{
    public function __construct(
        private GetImageThumbByRowUseCase $imageThumbUseCase,
    )
    {
    }

    /**
     * Строит ImageInfoData из строки выборки photos.
     *
     * В $row ожидаются поля:
     *  - id (ID родительской сущности),
     *  - photo_id, photo_file, model_type,
     *  - photo_alt, photo_title, photo_description,
     *  - photo_format, photo_width, photo_height.
     *
     * full и mini из БД не выбираются:
     *  - full = src (при $thumb === null возвращается оригинал),
     *  - mini = ''.
     */
    public function build(\stdClass $row, ?string $thumb = null): ImageInfoData
    {
        $src = $this->imageThumbUseCase->execute($row, $thumb);

        return new ImageInfoData(
            full: $src,
            src: $src,
            alt: $row->photo_alt ?? '',
            title: $row->photo_title ?? '',
            description: $row->photo_description ?? '',
            mini: '',
            format: $row->photo_format ?? null,
            width: $row->photo_width !== null ? (int)$row->photo_width : null,
            height: $row->photo_height !== null ? (int)$row->photo_height : null,
        );
    }
}
