<?php

namespace App\Modules\Marketplace\Infrastructure\Persistence;

use App\Modules\Marketplace\Domain\Entities\AvitoCommonImageEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCommonImageRepositoryInterface;
use App\Modules\Marketplace\Infrastructure\Models\AvitoCommonImage;
use App\Modules\Shared\Domain\ValueObjects\Image;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use Illuminate\Support\Facades\DB;

class AvitoCommonImageRepository implements AvitoCommonImageRepositoryInterface
{
    public function __construct(
        private readonly PhotoService $photoService,
    ) {}

    /**
     * @return AvitoCommonImageEntity[]
     */
    public function getAll(): array
    {
        return AvitoCommonImage::orderBy('sort')
            ->orderBy('id')
            ->with('image')
            ->get()
            ->map(fn (AvitoCommonImage $model) => $this->hydrate($model))
            ->all();
    }

    public function getById(int $id): AvitoCommonImageEntity
    {
        return $this->hydrate(AvitoCommonImage::with('image')->findOrFail($id));
    }

    public function save(AvitoCommonImageEntity $entity): AvitoCommonImageEntity
    {
        $model = $entity->id
            ? AvitoCommonImage::findOrFail($entity->id)
            : new AvitoCommonImage();

        $model->name = $entity->name;
        $model->active = $entity->active;
        $model->sort = $entity->sort;

        $model->save();

        return $this->hydrate($model->load('image'));
    }

    public function delete(int $id): void
    {
        $model = AvitoCommonImage::with('image')->findOrFail($id);
        $photo = $model->image;

        // Удаляем файлы (оригинал и нарезки) через драйвер хранилища (local/s3).
        if ($photo !== null && !empty($photo->file)) {
            $this->photoService->deleteFile(
                $photo->model_type,
                $photo->imageable_id,
                $photo->file,
            );
        }

        // Сначала удаляем родителя, затем запись Photo: после удаления родителя
        // imageable = null и boot-событие Photo не будет повторно удалять файлы
        // устаревшим локальным способом.
        $model->delete();

        if ($photo !== null && $photo->id !== null) {
            $photo->delete();
        }
    }

    public function reorder(array $ids): void
    {
        DB::transaction(function () use ($ids) {
            foreach ($ids as $sort => $id) {
                AvitoCommonImage::where('id', $id)->update(['sort' => $sort]);
            }
        });
    }

    public function nextSort(): int
    {
        return ((int) AvitoCommonImage::max('sort')) + 1;
    }

    private function hydrate(AvitoCommonImage $model): AvitoCommonImageEntity
    {
        $entity = new AvitoCommonImageEntity(name: $model->name);

        $entity->id = $model->id;
        $entity->active = (bool) $model->active;
        $entity->sort = (int) $model->sort;

        $photo = $model->image;
        if ($photo !== null && !empty($photo->file)) {
            // URL формируется через текущий драйвер хранилища (local/s3),
            // поэтому корректно работает и для PHOTO_STORAGE_DRIVER=s3.
            $url = $this->photoService->getUploadUrl(
                $photo->model_type,
                $photo->imageable_id,
                $photo->file,
            );

            if ($url !== '') {
                $entity->image = new Image($photo->id, $url);
            }
        }

        return $entity;
    }
}
