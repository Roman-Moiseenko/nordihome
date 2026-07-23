<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\Interfaces\RoomProductRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Models\RoomProduct;
use Illuminate\Pagination\LengthAwarePaginator;

class RoomProductRepository implements RoomProductRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getProductIdsByRoom(int $roomId, int $perPage = 15): LengthAwarePaginator
    {
        return RoomProduct::where('room_id', $roomId)
            ->select('product_id')
            ->paginate($perPage);
    }

    /**
     * @inheritDoc
     */
    public function getRoomsByProductId(int $productId): array
    {
        return RoomProduct::where('product_id', $productId)
            ->pluck('room_id')
            ->toArray();
    }

    /**
     * @inheritDoc
     */
    public function attachProducts(int $roomId, array $productIds): void
    {
        $existing = RoomProduct::where('room_id', $roomId)
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->toArray();

        $new = array_diff($productIds, $existing);

        foreach ($new as $productId) {
            $pivot = new RoomProduct();
            $pivot->room_id = $roomId;
            $pivot->product_id = $productId;
            $pivot->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function syncProducts(int $roomId, array $productIds): void
    {
        RoomProduct::where('room_id', $roomId)->delete();

        foreach ($productIds as $productId) {
            $pivot = new RoomProduct();
            $pivot->room_id = $roomId;
            $pivot->product_id = $productId;
            $pivot->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function detachProducts(int $roomId, array $productIds): void
    {
        RoomProduct::where('room_id', $roomId)
            ->whereIn('product_id', $productIds)
            ->delete();
    }

    /**
     * @inheritDoc
     */
    public function attachRooms(int $productId, array $roomIds): void
    {
        $existing = RoomProduct::where('product_id', $productId)
            ->whereIn('room_id', $roomIds)
            ->pluck('room_id')
            ->toArray();

        $new = array_diff($roomIds, $existing);

        foreach ($new as $roomId) {
            $pivot = new RoomProduct();
            $pivot->product_id = $productId;
            $pivot->room_id = $roomId;
            $pivot->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function syncRooms(int $productId, array $roomIds): void
    {
        RoomProduct::where('product_id', $productId)->delete();

        foreach ($roomIds as $roomId) {
            $pivot = new RoomProduct();
            $pivot->product_id = $productId;
            $pivot->room_id = $roomId;
            $pivot->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function detachRooms(int $productId, array $roomIds): void
    {
        RoomProduct::where('product_id', $productId)
            ->whereIn('room_id', $roomIds)
            ->delete();
    }
}
