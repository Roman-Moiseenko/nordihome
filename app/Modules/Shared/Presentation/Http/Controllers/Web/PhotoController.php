<?php

declare(strict_types=1);

namespace App\Modules\Shared\Presentation\Http\Controllers\Web;

use App\Modules\Shared\Application\Actions\ViewPhotoUseCase;
use App\Modules\Shared\Application\Actions\GetPhotoByEntityUseCase;
use App\Modules\Shared\Application\Actions\SavePhotoDataUseCase;
use App\Modules\Shared\Application\Actions\UploadPhotoUseCase;
use App\Modules\Shared\Application\Actions\GetPhotoThumbUseCase;
use App\Modules\Shared\Application\Actions\SortPhotoUseCase;
use App\Modules\Shared\Application\Actions\RemovePhotoUseCase;
use App\Modules\Shared\Application\DTOs\Photo\PhotoViewData;
use App\Modules\Shared\Application\DTOs\Photo\PhotoByEntityData;
use App\Modules\Shared\Application\DTOs\Photo\PhotoSaveData;
use App\Modules\Shared\Application\DTOs\Photo\PhotoUploadData;
use App\Modules\Shared\Application\DTOs\Photo\PhotoThumbData;
use App\Modules\Shared\Application\DTOs\Photo\PhotoSortData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PhotoController
{
    public function __construct(
        public readonly ViewPhotoUseCase        $viewPhotoUseCase,
        public readonly GetPhotoByEntityUseCase $getPhotoByEntityUseCase,
        public readonly SavePhotoDataUseCase    $savePhotoDataUseCase,
        public readonly UploadPhotoUseCase      $uploadPhotoUseCase,
        public readonly GetPhotoThumbUseCase    $getPhotoThumbUseCase,
        public readonly SortPhotoUseCase        $sortPhotoUseCase,
        public readonly RemovePhotoUseCase      $removePhotoUseCase,
    )
    {
    }

    /**
     * Получение данных об изображении по его id
     */
    public function getById(int $id, UserPermission $userPermission): JsonResponse
    {
        $photo = $this->viewPhotoUseCase->execute($id, $userPermission);
        return response()->json(
            PhotoViewData::fromEntity($photo),
            Response::HTTP_OK
        );
    }

    /**
     * Сохранение данных для photo (alt, title, description)
     */
    public function saveData(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $dto = PhotoSaveData::validateAndCreate($request->all());
        $this->savePhotoDataUseCase->execute($id, $dto, $userPermission);
        return response()->json(['message' => 'Сохранено'], Response::HTTP_OK);
    }

    /**
     * Загрузка фото для сущности
     * Параметры: file (UploadedFile), entity_id, model_type ({модуль}.{сущность}), type (icon/image/gallery)
     */
    public function upload(Request $request, UserPermission $userPermission): JsonResponse
    {
        $dto = PhotoUploadData::validateAndCreate($request->all());
        $dto->file  = $request->file('file');
        $photo = $this->uploadPhotoUseCase->execute($dto, $userPermission);
        return response()->json(
            PhotoViewData::fromEntity($photo),
            Response::HTTP_OK
        );
    }

    /**
     * Получение url и id по данным из сущности
     * Параметры: entity_id, model_type ({модуль}.{сущность}), type (icon/image/gallery)
     */
    public function getByEntity(Request $request, UserPermission $userPermission): JsonResponse
    {
        $dto = PhotoByEntityData::validateAndCreate($request->all());
        $photo = $this->getPhotoByEntityUseCase->execute($dto, $userPermission);

        if ($photo === null) {
            return response()->json(null, Response::HTTP_OK);
        }

        return response()->json(
            PhotoViewData::fromEntity($photo),
            Response::HTTP_OK
        );
    }

    /**
     * Получение url копии изображения по данным сущности + название копии
     * Параметры: entity_id, model_type ({модуль}.{сущность}), type (icon/image/gallery), thumb (название копии)
     */
    public function getThumb(Request $request, UserPermission $userPermission): JsonResponse
    {
        $dto = PhotoThumbData::validateAndCreate($request->all());
        $thumbUrl = $this->getPhotoThumbUseCase->execute($dto, $userPermission);
        return response()->json(['url' => $thumbUrl], Response::HTTP_OK);
    }

    /**
     * Изменение порядка сортировки для фото
     * Параметры: sort (новое значение сортировки)
     */
    public function sort(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $dto = PhotoSortData::validateAndCreate($request->all());
        $this->sortPhotoUseCase->execute($id, $dto, $userPermission);
        return response()->json(['message' => 'Сортировка обновлена'], Response::HTTP_OK);
    }

    /**
     * Удаление изображения по его id
     */
    public function destroy(int $id, UserPermission $userPermission): JsonResponse
    {
        $this->removePhotoUseCase->execute($id, $userPermission);
        return response()->json(['message' => 'Изображение удалено'], Response::HTTP_OK);
    }
}
