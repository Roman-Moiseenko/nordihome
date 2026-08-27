<?php

declare(strict_types=1);

namespace App\Modules\Discount\Infrastructure\Persistence;

use App\Modules\Discount\Application\Interfaces\PromotionProductRepositoryInterface;
use App\Modules\Discount\Infrastructure\Models\PromotionProduct;
use Illuminate\Pagination\LengthAwarePaginator;

class PromotionProductRepository implements PromotionProductRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getProductIdsByPromotionId(int $promotionId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return PromotionProduct::where('promotion_id', $promotionId)
            ->select('product_id', 'price')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @inheritDoc
     */
    public function attachProducts(int $promotionId, array $products): void
    {
        $existing = PromotionProduct::where('promotion_id', $promotionId)
            ->whereIn('product_id', array_keys($products))
            ->pluck('product_id')
            ->toArray();

        foreach ($products as $productId => $price) {
            if (in_array($productId, $existing, true)) {
                continue;
            }

            $pivot = new PromotionProduct();
            $pivot->promotion_id = $promotionId;
            $pivot->product_id = $productId;
            $pivot->price = $price;
            $pivot->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function syncProducts(int $promotionId, array $products): void
    {
        PromotionProduct::where('promotion_id', $promotionId)->delete();

        foreach ($products as $productId => $price) {
            $pivot = new PromotionProduct();
            $pivot->promotion_id = $promotionId;
            $pivot->product_id = $productId;
            $pivot->price = $price;
            $pivot->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function detachProducts(int $promotionId, array $productIds): void
    {
        PromotionProduct::where('promotion_id', $promotionId)
            ->whereIn('product_id', $productIds)
            ->delete();
    }

    /**
     * @inheritDoc
     */
    public function setPrice(int $promotionId, int $productId, float $price): void
    {
        $pivot = PromotionProduct::where('promotion_id', $promotionId)
            ->where('product_id', $productId)
            ->first();

        if ($pivot !== null) {
            $pivot->price = $price;
            $pivot->save();
        }
    }
}
