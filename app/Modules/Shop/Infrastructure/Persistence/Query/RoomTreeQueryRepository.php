<?php

declare(strict_types=1);

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Shared\Application\Actions\GetImageThumbByRowUseCase;
use App\Modules\Shop\Application\DTOs\RoomTreeClientData;
use Illuminate\Support\Facades\DB;

class RoomTreeQueryRepository
{
    private const string MODEL_TYPE = 'catalog.room';

    public function __construct(
        private readonly GetImageThumbByRowUseCase $imageThumbUseCase,

    )
    {
    }

    /** @return RoomTreeClientData[] */
    public function getFullTree(): array
    {
        $rows = DB::table('rooms')
            ->where('rooms.published', true)
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
                'photos.model_type as model_type',
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
                    image: $this->imageThumbUseCase->execute($item, 'catalog'),
                    children: $children
                );
            }
        }
        return $tree;
    }


    /**
     * Получить детей категории. Если $parentId = null — корневые категории.
     * @return RoomTreeClientData[]
     */
    public function getChildren(?int $parentId = null): array
    {
        $query = DB::table('rooms')
            ->where('rooms.published', true)
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
                'photos.model_type as model_type',
            )
            ->orderBy('rooms._lft');

        if ($parentId === null) {
            $query->whereNull('rooms.parent_id')
                ->where('rooms.slug', '<>', 'no_parse');
        } else {
            $query->where('rooms.parent_id', $parentId);
        }

        $rows = $query->get();

        return $rows->map(fn($row) => new RoomTreeClientData(
            id: $row->id,
            name: $row->name,
            slug: $row->slug,
            svg: $row->svg ?? '',
            image: $this->imageThumbUseCase->execute($row, 'catalog'),
            children: [],
        ))->all();
    }
}
