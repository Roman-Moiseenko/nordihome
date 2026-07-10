<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Shop\Application\DTOs\CategoryTreeClientData;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use Illuminate\Support\Facades\DB;

class CategoryTreeQueryRepository
{
    private const string MODEL_TYPE = 'catalog.category';

    public function __construct(
        private readonly PhotoService $photoService,
    )
    {
    }

    /** @return CategoryTreeClientData[] */
    public function getFullTree(): array
    {
        $rows = DB::table('categories')
            ->leftJoin('photos', function ($join) {
                $join->on('categories.id', '=', 'photos.imageable_id')
                    ->where('photos.model_type', '=', self::MODEL_TYPE)
                    ->where('photos.type', '=', 'image');
            })
            ->select(
                'categories.id',
                'categories.name',
                'categories.slug',
                'categories.svg',
                'categories.parent_id',
                'photos.id as photo_id',
                'photos.file as photo_file',
                'photos.thumb as photo_thumb',
            )
            ->orderBy('categories._lft')
            ->get();

        return $this->buildTree($rows);
    }

    private function buildTree($flatItems, ?int $parentId = null): array
    {
        $tree = [];
        foreach ($flatItems as $item) {
            if ($item->parent_id == $parentId) {
                $children = $this->buildTree($flatItems, $item->id);
                $tree[] = new CategoryTreeClientData(
                    id: $item->id,
                    name: $item->name,
                    slug: $item->slug,
                    svg: $item->svg ?? '',
                    image: $this->buildImageUrl($item),
                    children: $children
                );
            }
        }
        return $tree;
    }

    private function buildImageUrl(\stdClass $item): string
    {
        if (empty($item->photo_file)) {
            return '';
        }

        return $this->photoService->getThumbUrl(
            photoId: (int) $item->photo_id,
            modelType: self::MODEL_TYPE,
            imageableId: (int) $item->id,
            fileName: $item->photo_file,
            thumb: 'catalog',
            isThumbEnabled: (bool) $item->photo_thumb,
        );
    }

    /**
     * Получить детей категории. Если $parentId = null — корневые категории.
     * @return CategoryTreeClientData[]
     */
    public function getChildren(?int $parentId = null): array
    {
        $query = DB::table('categories')
            ->leftJoin('photos', function ($join) {
                $join->on('categories.id', '=', 'photos.imageable_id')
                    ->where('photos.model_type', '=', self::MODEL_TYPE)
                    ->where('photos.type', '=', 'image');
            })
            ->select(
                'categories.id',
                'categories.name',
                'categories.slug',
                'categories.svg',
                'categories.parent_id',
                'photos.id as photo_id',
                'photos.file as photo_file',
                'photos.thumb as photo_thumb',
            )
            ->orderBy('categories._lft');

        if ($parentId === null) {
            $query->whereNull('categories.parent_id')
                ->where('categories.slug', '<>', 'no_parse');
        } else {
            $query->where('categories.parent_id', $parentId);
        }

        $rows = $query->get();

        return $rows->map(fn($row) => new CategoryTreeClientData(
            id: $row->id,
            name: $row->name,
            slug: $row->slug,
            svg: $row->svg ?? '',
            image: $this->buildImageUrl($row),
            children: [],
        ))->all();
    }
}
