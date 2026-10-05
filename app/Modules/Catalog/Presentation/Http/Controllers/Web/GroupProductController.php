<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Modules\Catalog\Application\Actions\GroupProduct\AssignProductsToGroupUseCase;
use App\Modules\Catalog\Application\Actions\GroupProduct\AttachProductsToGroupUseCase;
use App\Modules\Catalog\Application\Actions\GroupProduct\DetachProductsFromGroupUseCase;
use App\Modules\Catalog\Application\Actions\GroupProduct\ListProductByGroupUseCase;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class GroupProductController
{
    public function __construct(
        private ListProductByGroupUseCase       $listProductByGroupUseCase,
        private AssignProductsToGroupUseCase    $assignProductsToGroupUseCase,
        private AttachProductsToGroupUseCase    $attachProductsToGroupUseCase,
        private DetachProductsFromGroupUseCase  $detachProductsFromGroupUseCase,
    )
    {
    }

    /**
     * Список товаров в группе (с пагинацией).
     * GET /admin/catalog/group/{id}/products
     */
    public function groupProducts(int $id, Request $request): JsonResponse
    {
        $page = $request->integer('page', 1);
        $perPage = $request->integer('per_page', 15);

        $list = $this->listProductByGroupUseCase->execute($id, $perPage, $page);

        return response()->json($list, Response::HTTP_OK);
    }

    /**
     * Назначить товары группе (sync — заменяет весь набор).
     * POST /admin/catalog/group/{id}/products/sync
     */
    public function assignGroupProducts(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $productIds = $request->input('products', []);

        $this->assignProductsToGroupUseCase->execute($id, $productIds, $userPermission);

        return response()->json(['message' => 'Товары назначены'], Response::HTTP_OK);
    }

    /**
     * Добавить товары к группе (attach — дополняет существующие).
     * POST /admin/catalog/group/{id}/products/attach
     */
    public function attachGroupProducts(int $id, Request $request, UserPermission $userPermission)
    {
        if ($request->has('product_id')) {
            $productIds[] = $request->integer('product_id');
        } else {
            $data = $request->input('products', []);
            if (count($data) == 0) throw new \DomainException('Нет данных');

            if (is_array($data[0])) {
                foreach ($data as $item) {
                    $productIds[] = $item['product_id'];
                }
            } else {
                $productIds = $data;
            }
        }

        $this->attachProductsToGroupUseCase->execute($id, $productIds ?? [], $userPermission);

        return redirect()->back()->with('success', 'Товары добавлены');
    }

    /**
     * Отвязать товары от группы.
     * DELETE /admin/catalog/group/{id}/products/detach
     */
    public function detachGroupProducts(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $productIds = $request->input('products', []);

        $this->detachProductsFromGroupUseCase->execute($id, $productIds, $userPermission);

        return response()->json(['message' => 'Товары откреплены'], Response::HTTP_OK);
    }
}
