<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Actions;

use App\Modules\Base\Service\HttpPage;
use App\Modules\Shared\Application\DTOs\Photo\PhotoUploadByUrlData;
use App\Modules\Shared\Application\DTOs\Photo\PhotoUploadData;
use App\Modules\Shared\Domain\Entities\PhotoEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Setting\Entity\Settings;
use Illuminate\Http\UploadedFile;

readonly class UploadPhotoByUrlUseCase
{
    public function __construct(
        private UploadPhotoUseCase $uploadPhotoUseCase,
    )
    {
    }

    public function execute(PhotoUploadByUrlData $dto, UserPermission $userPermission): ?PhotoEntity
    {
        $uploadedFile = $this->downloadFile($dto->url);

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

    private function downloadFile(string $url): ?UploadedFile
    {
        $settings = app()->make(Settings::class);
        $isProxy = $settings->parser->with_proxy ?? false;

        $storage = public_path() . '/temp/';
        $uploadFileName = basename($url);
        $ext = pathinfo($uploadFileName, PATHINFO_EXTENSION);
        if (empty($ext)) {
            $ext = 'webp';
        }
        $fullFilename = $storage . uniqid() . '.' . $ext;

        try {
            if ($isProxy) {
                $http = new HttpPage();
                $content = $http->getPage($url);
                $fp = fopen($fullFilename, 'x');
                fwrite($fp, $content);
                fclose($fp);
            } else {
                copy($url, $fullFilename);
            }
        } catch (\Throwable $e) {
            \Log::error('Ошибка скачивания изображения: ' . $e->getMessage() . ' URL: ' . $url);
            return null;
        }

        if (!file_exists($fullFilename)) {
            return null;
        }

        return new UploadedFile(
            $fullFilename,
            $uploadFileName,
            null,
            null,
            true,
        );
    }
}
