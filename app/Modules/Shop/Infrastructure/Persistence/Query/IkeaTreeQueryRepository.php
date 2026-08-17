<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Shared\Application\Actions\GetImageThumbByRowUseCase;
use App\Modules\Shop\Application\DTOs\IkeaTreeClientData;
use Illuminate\Support\Facades\DB;

class IkeaTreeQueryRepository
{
    private const string MODEL_TYPE = 'parser.category';

    public function __construct(
        private readonly GetImageThumbByRowUseCase $imageThumbUseCase,
    )
    {
    }

    /** @return IkeaTreeClientData[] */
    public function getFullTree(): array
    {
        $rows = DB::table('parser_categories')
            ->where('parser_categories.active', true)
            ->leftJoin('photos', function ($join) {
                $join->on('parser_categories.id', '=', 'photos.imageable_id')
                    ->where('photos.model_type', '=', self::MODEL_TYPE)
                    ->where('photos.type', '=', 'image');
            })
            ->select(
                'parser_categories.id',
                'parser_categories.name',
                'parser_categories.slug',
                'parser_categories.parent_id',
                'photos.id as photo_id',
                'photos.file as photo_file',
                'photos.model_type as model_type',
            )
            ->orderBy('parser_categories._lft')
            ->get();

        return $this->buildTree($rows);
    }

    private function buildTree($flatItems, ?int $parentId = null): array
    {
        $tree = [];
        foreach ($flatItems as $item) {
            if ($item->parent_id == $parentId) {
                $children = $this->buildTree($flatItems, $item->id);
                $tree[] = new IkeaTreeClientData(
                    id: $item->id,
                    name: $item->name,
                    slug: $item->slug,
                    image: $this->imageThumbUseCase->execute($item),
                    children: $children
                );
            }
        }
        return $tree;
    }

}
