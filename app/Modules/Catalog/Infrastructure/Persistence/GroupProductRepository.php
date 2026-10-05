<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Domain\Interfaces\GroupProductRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Models\GroupProduct;
use Illuminate\Pagination\LengthAwarePaginator;

class GroupProductRepository implements GroupProductRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getProductIdsByGroupId(int $groupId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return GroupProduct::where('group_id', $groupId)
            ->select('product_id')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @inheritDoc
     */
    public function syncProducts(int $groupId, array $productIds): void
    {
        GroupProduct::where('group_id', $groupId)->delete();

        foreach ($productIds as $productId) {
            $pivot = new GroupProduct();
            $pivot->group_id = $groupId;
            $pivot->product_id = $productId;
            $pivot->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function attachProducts(int $groupId, array $productIds): void
    {
        $existing = GroupProduct::where('group_id', $groupId)
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->toArray();

        $new = array_diff($productIds, $existing);

        foreach ($new as $productId) {
            $pivot = new GroupProduct();
            $pivot->group_id = $groupId;
            $pivot->product_id = $productId;
            $pivot->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function detachProducts(int $groupId, array $productIds): void
    {
        GroupProduct::where('group_id', $groupId)
            ->whereIn('product_id', $productIds)
            ->delete();
    }

    /**
     * @inheritDoc
     */
    public function detachAllProducts(int $groupId): void
    {
        GroupProduct::where('group_id', $groupId)->delete();
    }
}
