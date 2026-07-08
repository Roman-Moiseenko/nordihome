<?php

declare(strict_types=1);

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Shop\Application\DTOs\RoomTreeClientData;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use Illuminate\Support\Facades\DB;

class RoomTreeQueryRepository
{
    private const string MODEL_TYPE = 'catalog.room';

    public function __construct(
        private readonly PhotoService $photoService,
    )
    {
    }

    /** @return RoomTreeClientData[] */
    public function getFullTree(): array
    {
        $rows = DB::table('rooms')
            ->leftJoin('photos', function ($join) {
                $join->on('rooms.id', '=', 'photos.imageable_id')
                    ->where('photos.model_type', '=', self::MODEL_TYPE)
                    ->where('photos.type', '=', 'image');
            })
            ->select(
                'rooms.id',
                'rooms.name',
                'rooms.slug',
                'rooms.svg',
                'rooms.parent_id',
                'photos.id as photo_id',
                'photos.file as photo_file',
                'photos.thumb as photo_thumb',
            )
            ->orderBy('rooms._lft')
            ->get();

        return $this->buildTree($rows);
    }

    private function buildTree($flatItems, ?int $parentId = null): array
    {
        $tree = [];
        foreach ($flatItems as $item) {
            if ($item->parent_id == $parentId) {
                $children = $this->buildTree($flatItems, $item->id);
                $tree[] = new RoomTreeClientData(
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
}
