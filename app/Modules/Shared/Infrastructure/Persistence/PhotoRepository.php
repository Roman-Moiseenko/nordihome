<?php

declare(strict_types=1);

namespace App\Modules\Shared\Infrastructure\Persistence;

use App\Modules\Shared\Application\Interfaces\PhotoRepositoryInterface;
use App\Modules\Shared\Domain\Entities\PhotoEntity;
use App\Modules\Shared\Domain\ValueObjects\PhotoType;
use App\Modules\Shared\Infrastructure\Models\Photo;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class PhotoRepository implements PhotoRepositoryInterface
{
    public function __construct(
        private readonly PhotoService $photoService,
    )
    {
    }

    public function save(PhotoEntity $photo): PhotoEntity
    {
        /*  $model = $photo->id
              ? Photo::findOrFail($photo->id)
              : new Photo();

          // Если есть файл для загрузки, обрабатываем его через серв
       /*
        *    if (isset($photo->fileForUpload) && $photo->fileForUpload instanceof UploadedFile) {
              $photo->file = $this->photoService->uploadFile(
                  $photo->modelType,
                  $photo->imageableId,
                  $photo->fileForUpload,
                  $model->file ?? null,
                  $photo->thumb,
              );
              unset($photo->fileForUpload);
          }

  */
        if ($photo->type->isSingle()) {
            // Для одиночных типов — удаляем старую запись (если есть) и создаём новую
            Photo::where([
                'model_type' => $photo->modelType,
                'imageable_id' => $photo->imageableId,
                'type' => $photo->type->getValue(),
            ])->delete();

            $model = new Photo();
            $model->sort = 0;
        } else {
            // Галерея
            if ($photo->id) {
                $model = Photo::findOrFail($photo->id);
                $oldSort = $model->sort;
                if ($oldSort !== $photo->sort) {
                    $this->reorderGalleryAfterChange($photo->modelType, $photo->imageableId, $photo->type->getValue(), $photo->id, $oldSort, $photo->sort);
                    $model->sort = $photo->sort;
                }
            } else {
                $model = new Photo();
                $maxSort = Photo::where([
                    'model_type' => $photo->modelType,
                    'imageable_id' => $photo->imageableId,
                    'type' => $photo->type->getValue(),
                ])->max('sort');
                $model->sort = is_null($maxSort) ? 0 : $maxSort + 1;
            }
        }

        $model->imageable_id = $photo->imageableId;
        $model->imageable_type = $photo->imageableType;
        $model->model_type = $photo->modelType;
        $model->file = $photo->file;
        $model->alt = $photo->alt;
        $model->slug = $photo->slug;
        $model->title = $photo->title;
        $model->description = $photo->description;
        // $model->sort = $photo->sort;
        $model->type = (string)$photo->type;
        $model->save();

        // Создаём thumbs при сохранении, если включено
        /*
        if ($photo->thumb && $this->photoService->createThumbsOnSave) {
            $this->photoService->createThumbs(
                $model->id,
                $photo->modelType,
                $photo->imageableId,
                $model->file,
            );
        }
*/
        return $this->hydrate($model->fresh());
    }

    public function getById(int $id): PhotoEntity
    {
        $model = Photo::findOrFail($id);
        return $this->hydrate($model);
    }

    public function findByEntity(int $imageableId, string $modelType, PhotoType $type): ?PhotoEntity
    {
        $model = Photo::orderBy('sort')
            ->where('imageable_id', $imageableId)
            ->where('model_type', $modelType)
            ->where('type', $type->getValue())
            ->first();

        if ($model === null) {
            return null;
        }

        return $this->hydrate($model);
    }

    public function findAllByEntity(int $imageableId, string $modelType, PhotoType $type): array
    {
        $models = Photo::orderBy('sort')
            ->where('imageable_id', $imageableId)
            ->where('model_type', $modelType)
            ->where('type', $type->getValue())
            ->get();

        if ($models === null) return [];

        return $models->map(fn($model) => $this->hydrate($model))->toArray();
    }

    public function findByEntities(array $imageableIds, string $modelType, PhotoType $type): array
    {
        if (empty($imageableIds)) {
            return [];
        }

        // Берём первую запись для каждого imageable_id (по sort ASC)
        $ids = DB::table('photos as p')
            ->select(DB::raw('MIN(p.id) as id'))
            ->whereIn('p.imageable_id', $imageableIds)
            ->where('p.model_type', $modelType)
            ->where('p.type', $type->getValue())
            ->groupBy('p.imageable_id')
            ->pluck('id');

        if ($ids->isEmpty()) {
            return [];
        }

        $models = Photo::whereIn('id', $ids)->get();

        $result = [];
        foreach ($models as $model) {
            $result[$model->imageable_id] = $this->photoService->getUploadUrl(
                $model->model_type,
                $model->imageable_id,
                $model->file,
            );
        }

        return $result;
    }


    private function getSort(PhotoEntity $photo)
    {
        if ($photo->id != null) return $photo->sort; //Для изобр. уже созданных возвращаем его sort
        //Для новых находим


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


    private function reorderGalleryAfterChange(string $modelType, int $modelId, string $type, int $mediaId, int $oldSort, int $newSort): void
    {
        // Получаем все элементы галереи, кроме текущего, отсортированные по sort
        $items = Photo::where([
            'model_type' => $modelType,
            'imageable_id' => $modelId,
            'type' => $type,
        ])->where('id', '!=', $mediaId)
            ->orderBy('sort')
            ->get();

        // Перестраиваем массив сортов после удаления старой позиции
        $sorts = $items->pluck('sort')->toArray();

        // Удаляем старый sort из набора (он не в элементах, но в последовательности)
        // Сдвигаем все элементы, которые были > oldSort, на -1
        foreach ($items as $item) {
            if ($item->sort > $oldSort) {
                $item->sort--;
            }
        }

        // Вставляем элемент на позицию newSort
        // Элементы с sort >= newSort сдвигаем +1
        foreach ($items as $item) {
            if ($item->sort >= $newSort) {
                $item->sort++;
            }
        }

        // Сохраняем изменения
        foreach ($items as $item) {
            $item->save();

        }
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

        // Генерируем uploadUrl
        $entity->uploadUrl = $this->photoService->getUploadUrl(
            $model->model_type,
            $model->imageable_id,
            $model->file,
        );

        return $entity;
    }
}
