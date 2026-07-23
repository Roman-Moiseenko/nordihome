<?php

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\Interfaces\TagProductRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Models\TagProduct;


class TagProductRepository implements TagProductRepositoryInterface
{
    public function countProductsByTagIds(array $tagIds): array
    {
        if (empty($tagIds)) {
            return [];
        }

        return TagProduct::select('tag_id')
            ->selectRaw('COUNT(*) as count')
            ->whereIn('tag_id', $tagIds)
            ->groupBy('tag_id')
            ->pluck('count', 'tag_id')
            ->toArray();
    }

    public function countProductsByTagId(int $tagId): int
    {
        return TagProduct::where('tag_id', $tagId)->count();
    }
}
