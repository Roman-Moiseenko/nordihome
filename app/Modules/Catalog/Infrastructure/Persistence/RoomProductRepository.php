<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\DTOs\Product\ProductRoomData;
use App\Modules\Catalog\Application\DTOs\Room\RoomProductData;
use App\Modules\Catalog\Application\Interfaces\RoomProductRepositoryInterface;
use App\Modules\Catalog\Entity\Product;
use App\Modules\Catalog\Infrastructure\Models\RoomProduct;
use App\Modules\Catalog\Infrastructure\Models\Room;
use Illuminate\Pagination\LengthAwarePaginator;

class RoomProductRepository implements RoomProductRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getProductsByRoomId(int $roomId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $productIds = RoomProduct::where('room_id', $roomId)
            ->pluck('product_id');

        return Product::orderBy('name')
            ->whereIn('id', $productIds)
            ->where(function ($query) {
                $query->doesntHave('modification')->orHas('main_modification');
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(fn(Product $product) => new ProductRoomData(
                id: $product->id,
                code: $product->code,
                name: $product->name,
                image: $product->miniImage(),
                published: (bool) $product->published,
                not_sale: (bool) $product->not_sale,
            ));
    }

    /**
     * @inheritDoc
     */
    public function getRoomsByProductId(int $productId): array
    {
        $roomIds = RoomProduct::where('product_id', $productId)
            ->pluck('room_id');

        return Room::whereIn('id', $roomIds)
            ->orderBy('name')
            ->get()
            ->map(fn(Room $room) => new RoomProductData(
                id: $room->id,
                name: $room->name,
                slug: $room->slug,
            ))
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
