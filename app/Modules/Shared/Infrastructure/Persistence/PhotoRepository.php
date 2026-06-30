<?php

declare(strict_types=1);

namespace App\Modules\Shared\Infrastructure\Persistence;

use App\Modules\Shared\Application\Interfaces\PhotoRepositoryInterface;
use App\Modules\Shared\Domain\Entities\PhotoEntity;
use App\Modules\Shared\Domain\ValueObjects\PhotoType;
use App\Modules\Shared\Infrastructure\Models\Photo;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use Illuminate\Http\UploadedFile;

class PhotoRepository implements PhotoRepositoryInterface
{
    public function __construct(
        private readonly PhotoService $photoService,
    )
    {
    }

    public function getById(int $id): PhotoEntity
    {
        $model = Photo::findOrFail($id);
        return $this->hydrate($model);
    }

    public function findByEntity(int $imageableId, string $modelType, PhotoType $type): ?PhotoEntity
    {
        $model = Photo::where('imageable_id', $imageableId)
            ->where('model_type', $modelType)
            ->where('type', $type->getValue())
            ->first();

        if ($model === null) {
            return null;
        }

        return $this->hydrate($model);
    }

    public function save(PhotoEntity $photo): PhotoEntity
    {
        $model = $photo->id
            ? Photo::findOrFail($photo->id)
            : new Photo();

        // Если есть файл для загрузки, обрабатываем его через сервис
        if (isset($photo->fileForUpload) && $photo->fileForUpload instanceof UploadedFile) {
            $model->file = $this->photoService->uploadFile(
                $photo->modelType,
                $photo->imageableId,
                $photo->fileForUpload,
                $model->file ?? null,
                $photo->thumb,
            );
            unset($photo->fileForUpload);
        }

        $model->imageable_id = $photo->imageableId;
        $model->imageable_type = $photo->imageableType;
        $model->model_type = $photo->modelType;
        $model->file = $photo->file;
        $model->alt = $photo->alt;
        $model->slug = $photo->slug;
        $model->title = $photo->title;
        $model->description = $photo->description;
        $model->sort = $photo->sort;
        $model->type = (string) $photo->type;
        $model->thumb = $photo->thumb;

        $model->save();

        // Создаём thumbs при сохранении, если включено
        if ($photo->thumb && $this->photoService->createThumbsOnSave) {
            $this->photoService->createThumbs(
                $model->id,
                $photo->modelType,
                $photo->imageableId,
                $model->file,
            );
        }

        return $this->hydrate($model->fresh());
    }

    public function update(int $id, array $data): PhotoEntity
    {
        $model = Photo::findOrFail($id);

        if (isset($data['file']) && $data['file'] instanceof UploadedFile) {
            $data['file'] = $this->photoService->uploadFile(
                $model->model_type,
                $model->imageable_id,
                $data['file'],
                $model->file,
                $data['thumb'] ?? $model->thumb,
            );
        }

        $model->update($data);

        return $this->hydrate($model->fresh());
    }

    public function delete(int $id): void
    {
        $model = Photo::findOrFail($id);

        // Удаляем файлы
        $this->photoService->deleteFile(
            $model->model_type,
            $model->imageable_id,
            $model->file,
        );

        $model->delete();
    }

    private function hydrate(Photo $model): PhotoEntity
    {
        $entity = new PhotoEntity(
            imageableId: $model->imageable_id,
            imageableType: $model->imageable_type,
            modelType: $model->model_type,
            file: $model->file,
            type: new PhotoType($model->type),
        );

        $entity->id = $model->id;
        $entity->alt = $model->alt ?? '';
        $entity->slug = $model->slug ?? '';
        $entity->title = $model->title ?? '';
        $entity->description = $model->description ?? '';
        $entity->sort = $model->sort ?? 0;
        $entity->thumb = (bool) ($model->thumb ?? true);

        // Генерируем uploadUrl
        $entity->uploadUrl = $this->photoService->getUploadUrl(
            $model->model_type,
            $model->imageable_id,
            $model->file,
        );

        return $entity;
    }
}
