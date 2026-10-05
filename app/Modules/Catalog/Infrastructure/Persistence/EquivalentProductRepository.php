<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Domain\Interfaces\EquivalentProductRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Models\EquivalentProduct;
use Illuminate\Pagination\LengthAwarePaginator;

class EquivalentProductRepository implements EquivalentProductRepositoryInterface
{
    public function getProductIdsByEquivalentId(int $equivalentId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return EquivalentProduct::where('equivalent_id', $equivalentId)
            ->select('product_id')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function getEquivalentIdsByProductId(int $productId): array
    {
        return EquivalentProduct::where('product_id', $productId)
            ->pluck('equivalent_id')
            ->toArray();
    }

    public function attachProducts(int $equivalentId, array $productIds): void
    {
        $existing = EquivalentProduct::where('equivalent_id', $equivalentId)
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->toArray();

        $new = array_diff(array_unique($productIds), $existing);

        foreach ($new as $productId) {
            $pivot = new EquivalentProduct();
            $pivot->equivalent_id = $equivalentId;
            $pivot->product_id = $productId;
            $pivot->save();
        }
    }

    public function syncProducts(int $equivalentId, array $productIds): void
    {
        EquivalentProduct::where('equivalent_id', $equivalentId)->delete();

        foreach (array_unique($productIds) as $productId) {
            $pivot = new EquivalentProduct();
            $pivot->equivalent_id = $equivalentId;
            $pivot->product_id = $productId;
            $pivot->save();
        }
    }

    public function detachProducts(int $equivalentId, array $productIds): void
    {
        EquivalentProduct::where('equivalent_id', $equivalentId)
            ->whereIn('product_id', $productIds)
            ->delete();
    }

    public function detachAllProducts(int $equivalentId): void
    {
        EquivalentProduct::where('equivalent_id', $equivalentId)->delete();
    }

    public function attachEquivalents(int $productId, array $equivalentIds): void
    {
        $existing = EquivalentProduct::where('product_id', $productId)
            ->whereIn('equivalent_id', $equivalentIds)
            ->pluck('equivalent_id')
            ->toArray();

        $new = array_diff(array_unique($equivalentIds), $existing);

        foreach ($new as $equivalentId) {
            $pivot = new EquivalentProduct();
            $pivot->product_id = $productId;
            $pivot->equivalent_id = $equivalentId;
            $pivot->save();
        }
    }

    public function syncEquivalents(int $productId, array $equivalentIds): void
    {
        EquivalentProduct::where('product_id', $productId)->delete();

        foreach (array_unique($equivalentIds) as $equivalentId) {
            $pivot = new EquivalentProduct();
            $pivot->product_id = $productId;
            $pivot->equivalent_id = $equivalentId;
            $pivot->save();
        }
    }

    public function detachEquivalents(int $productId, array $equivalentIds): void
    {
        EquivalentProduct::where('product_id', $productId)
            ->whereIn('equivalent_id', $equivalentIds)
            ->delete();
    }

    public function countProductsByEquivalentIds(array $equivalentIds): array
    {
        if (empty($equivalentIds)) {
            return [];
        }

        return EquivalentProduct::select('equivalent_id')
            ->selectRaw('COUNT(*) as count')
            ->whereIn('equivalent_id', $equivalentIds)
            ->groupBy('equivalent_id')
            ->pluck('count', 'equivalent_id')
            ->toArray();
    }
}
