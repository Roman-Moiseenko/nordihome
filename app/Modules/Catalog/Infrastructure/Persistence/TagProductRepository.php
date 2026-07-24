<?php

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\Interfaces\TagProductRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Models\TagProduct;
use Illuminate\Pagination\LengthAwarePaginator;


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

    public function getTagsByProductId(int $productId): array
    {
        return TagProduct::where('product_id', $productId)
            ->pluck('tag_id')
            ->toArray();
    }

    public function syncProducts(int $tagId, array $productIds): void
    {
        TagProduct::where('tag_id', $tagId)->delete();

        foreach ($productIds as $productId) {
            $pivot = new TagProduct();
            $pivot->tag_id = $tagId;
            $pivot->product_id = $productId;
            $pivot->save();
        }
    }

    public function syncTags(int $productId, array $tagIds): void
    {
        TagProduct::where('product_id', $productId)->delete();

        foreach ($tagIds as $tagId) {
            $pivot = new TagProduct();
            $pivot->product_id = $productId;
            $pivot->tag_id = $tagId;
            $pivot->save();
        }
    }

    public function attachProducts(int $tagId, array $productIds): void
    {
        $existing = TagProduct::where('tag_id', $tagId)
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->toArray();

        $new = array_diff($productIds, $existing);

        foreach ($new as $productId) {
            $pivot = new TagProduct();
            $pivot->tag_id = $tagId;
            $pivot->product_id = $productId;
            $pivot->save();
        }
    }

    public function attachTags(int $productId, array $tagIds): void
    {
        $existing = TagProduct::where('product_id', $productId)
            ->whereIn('tag_id', $tagIds)
            ->pluck('tag_id')
            ->toArray();

        $new = array_diff($tagIds, $existing);

        foreach ($new as $tagId) {
            $pivot = new TagProduct();
            $pivot->product_id = $productId;
            $pivot->tag_id = $tagId;
            $pivot->save();
        }
    }

    public function detachProducts(int $tagId, array $productIds): void
    {
        TagProduct::where('tag_id', $tagId)
            ->whereIn('product_id', $productIds)
            ->delete();
    }

    public function detachTags(int $productId, array $tagIds): void
    {
        TagProduct::where('product_id', $productId)
            ->whereIn('tag_id', $tagIds)
            ->delete();
    }

    public function getProductIdsByTag(int $tagId, int $perPage = 15): LengthAwarePaginator
    {
        return TagProduct::where('tag_id', $tagId)
            ->select('product_id')
            ->paginate($perPage);
    }
}
