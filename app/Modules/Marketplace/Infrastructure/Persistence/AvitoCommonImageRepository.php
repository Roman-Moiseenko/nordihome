<?php

namespace App\Modules\Marketplace\Infrastructure\Persistence;

use App\Modules\Marketplace\Domain\Entities\AvitoCommonImageEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCommonImageRepositoryInterface;
use App\Modules\Marketplace\Infrastructure\Models\AvitoCommonImage;
use App\Modules\Shared\Domain\ValueObjects\Image;
use Illuminate\Support\Facades\DB;

class AvitoCommonImageRepository implements AvitoCommonImageRepositoryInterface
{
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
        AvitoCommonImage::findOrFail($id)->delete();
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

        $url = $model->image->getUploadUrl();
        if ($url !== '') {
            $entity->image = new Image($model->image->id ?? null, $url);
        }

        return $entity;
    }
}
