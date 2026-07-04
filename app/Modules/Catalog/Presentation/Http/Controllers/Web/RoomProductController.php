<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Modules\Catalog\Application\Actions\RoomProduct\AssignProductsToRoomUseCase;
use App\Modules\Catalog\Application\Actions\RoomProduct\AssignRoomsToProductUseCase;
use App\Modules\Catalog\Application\Actions\RoomProduct\AttachProductToRoomUseCase;
use App\Modules\Catalog\Application\Actions\RoomProduct\AttachRoomsToProductUseCase;
use App\Modules\Catalog\Application\Actions\RoomProduct\DetachProductFromRoomUseCase;
use App\Modules\Catalog\Application\Actions\RoomProduct\DetachRoomsFromProductUseCase;
use App\Modules\Catalog\Application\Actions\RoomProduct\ListProductByRoomUseCase;
use App\Modules\Catalog\Application\Actions\RoomProduct\ListRoomByProductUseCase;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class RoomProductController
{
    public function __construct(
        // Комната → Товары
        private ListProductByRoomUseCase      $listProductByRoomUseCase,
        private AssignProductsToRoomUseCase   $assignProductsToRoomUseCase,
        private AttachProductToRoomUseCase    $attachProductToRoomUseCase,
        private DetachProductFromRoomUseCase  $detachProductFromRoomUseCase,
        // Товар → Комнаты
        private ListRoomByProductUseCase      $listRoomByProductUseCase,
        private AssignRoomsToProductUseCase   $assignRoomsToProductUseCase,
        private AttachRoomsToProductUseCase   $attachRoomsToProductUseCase,
        private DetachRoomsFromProductUseCase $detachRoomsFromProductUseCase,
    )
    {
    }

    // ======================== Действия от комнаты ========================

    /**
     * Список товаров в комнате (с пагинацией).
     * GET /admin/catalog/room/{id}/products
     */
    public function roomProducts(int $id, Request $request): JsonResponse
    {
        $page = $request->integer('page', 1);
        $perPage = $request->integer('per_page', 15);

        $paginator = $this->listProductByRoomUseCase->execute($id, $perPage, $page);

        return response()->json($paginator, Response::HTTP_OK);
    }

    /**
     * Назначить товары комнате (sync — заменяет весь набор).
     * POST /admin/catalog/room/{id}/products/sync
     */
    public function assignRoomProducts(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $productIds = $request->input('products', []);


        $this->assignProductsToRoomUseCase->execute($id, $productIds, $userPermission);

        return response()->json(['message' => 'Товары назначены'], Response::HTTP_OK);
    }

    /**
     * Добавить товары к комнате (attach — дополняет существующие).
     * POST /admin/catalog/room/{id}/products/attach
     */
    public function attachRoomProducts(int $id, Request $request, UserPermission $userPermission)
    {
        if ($request->has('product_id')) {
            $productIds[] = $request->integer('product_id');
        } else {
            $data = $request->input('products', []);
            if (count($data) == 0) throw new \DomainException('Нет данных');

            if (is_array($data[0])) {
                foreach ($data as $item) {
                    $productIds[] =  $item['product_id'];
                }
            } else {
                $productIds = $data;
            }
        }

        $this->attachProductToRoomUseCase->execute($id, $productIds ?? [], $userPermission);
        return redirect()->back()->with('success', 'Товары добавлены');
        //return response()->json(['message' => 'Товары добавлены'], Response::HTTP_OK);
    }

    /**
     * Отвязать товары от комнаты.
     * DELETE /admin/catalog/room/{id}/products/detach
     */
    public function detachRoomProducts(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $productIds = $request->input('products', []);

        $this->detachProductFromRoomUseCase->execute($id, $productIds, $userPermission);

        return response()->json(['message' => 'Товары откреплены'], Response::HTTP_OK);
    }

    // ======================== Действия от товара ========================

    /**
     * Список комнат товара (массив).
     * GET /admin/catalog/product/{id}/rooms
     */
    public function productRooms(int $id): JsonResponse
    {
        $list = $this->listRoomByProductUseCase->execute($id);

        return response()->json($list, Response::HTTP_OK);
    }

    /**
     * Назначить комнаты товару (sync — заменяет весь набор).
     * POST /admin/catalog/product/{id}/rooms/sync
     */
    public function assignProductRooms(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $roomIds = $request->input('rooms', []);

        $this->assignRoomsToProductUseCase->execute($id, $roomIds, $userPermission);

        return response()->json(['message' => 'Комнаты назначены'], Response::HTTP_OK);
    }

    /**
     * Добавить комнаты к товару (attach — дополняет существующие).
     * POST /admin/catalog/product/{id}/rooms/attach
     */
    public function attachProductRooms(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $roomIds = $request->input('rooms', []);

        $this->attachRoomsToProductUseCase->execute($id, $roomIds, $userPermission);

        return response()->json(['message' => 'Комнаты добавлены'], Response::HTTP_OK);
    }

    /**
     * Отвязать комнаты от товара.
     * DELETE /admin/catalog/product/{id}/rooms/detach
     */
    public function detachProductRooms(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $roomIds = $request->input('rooms', []);

        $this->detachRoomsFromProductUseCase->execute($id, $roomIds, $userPermission);

        return response()->json(['message' => 'Комнаты откреплены'], Response::HTTP_OK);
    }
}
