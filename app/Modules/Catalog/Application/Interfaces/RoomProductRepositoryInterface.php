<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Interfaces;

use App\Modules\Catalog\Application\DTOs\Product\ProductRoomData;
use App\Modules\Catalog\Application\DTOs\Room\RoomProductData;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoomProductRepositoryInterface
{
    /**
     * Получить товары, привязанные к комнате (с пагинацией).
     *
     * @param int $roomId
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator<ProductRoomData>
     */
    public function getProductIdsByRoom(int $roomId, int $perPage = 15, int $page = 1): LengthAwarePaginator;

    /**
     * Получить комнаты, привязанные к товару (простой массив).
     *
     * @param int $productId
     * @return RoomProductData[]
     */
    public function getRoomsByProductId(int $productId): array;

    /**
     * Привязать товары к комнате (добавление к существующим).
     *
     * @param int   $roomId
     * @param int[] $productIds
     */
    public function attachProducts(int $roomId, array $productIds): void;

    /**
     * Синхронизировать товары комнаты (заменить весь набор).
     *
     * @param int   $roomId
     * @param int[] $productIds
     */
    public function syncProducts(int $roomId, array $productIds): void;

    /**
     * Отвязать товары от комнаты.
     *
     * @param int   $roomId
     * @param int[] $productIds
     */
    public function detachProducts(int $roomId, array $productIds): void;

    /**
     * Привязать комнаты к товару (добавление к существующим).
     *
     * @param int   $productId
     * @param int[] $roomIds
     */
    public function attachRooms(int $productId, array $roomIds): void;

    /**
     * Синхронизировать комнаты товара (заменить весь набор).
     *
     * @param int   $productId
     * @param int[] $roomIds
     */
    public function syncRooms(int $productId, array $roomIds): void;

    /**
     * Отвязать комнаты от товара.
     *
     * @param int   $productId
     * @param int[] $roomIds
     */
    public function detachRooms(int $productId, array $roomIds): void;
}
