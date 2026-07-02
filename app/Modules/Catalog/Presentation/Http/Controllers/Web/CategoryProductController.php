<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Modules\Catalog\Application\Actions\CategoryProduct\AssignCategoriesToProductUseCase;
use App\Modules\Catalog\Application\Actions\CategoryProduct\AssignProductsToCategoryUseCase;
use App\Modules\Catalog\Application\Actions\CategoryProduct\AttachCategoriesToProductUseCase;
use App\Modules\Catalog\Application\Actions\CategoryProduct\AttachProductToCategoryUseCase;
use App\Modules\Catalog\Application\Actions\CategoryProduct\DetachCategoriesFromProductUseCase;
use App\Modules\Catalog\Application\Actions\CategoryProduct\DetachProductFromCategoryUseCase;
use App\Modules\Catalog\Application\Actions\CategoryProduct\ListCategoryByProductUseCase;
use App\Modules\Catalog\Application\Actions\CategoryProduct\ListProductByCategoryUseCase;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class CategoryProductController
{
    public function __construct(
        // Категория → Товары
        private ListProductByCategoryUseCase       $listProductByCategoryUseCase,
        private AssignProductsToCategoryUseCase    $assignProductsToCategoryUseCase,
        private AttachProductToCategoryUseCase     $attachProductToCategoryUseCase,
        private DetachProductFromCategoryUseCase   $detachProductFromCategoryUseCase,
        // Товар → Категории
        private ListCategoryByProductUseCase       $listCategoryByProductUseCase,
        private AssignCategoriesToProductUseCase   $assignCategoriesToProductUseCase,
        private AttachCategoriesToProductUseCase   $attachCategoriesToProductUseCase,
        private DetachCategoriesFromProductUseCase $detachCategoriesFromProductUseCase,
    )
    {
    }

    // ======================== Действия от категории ========================

    /**
     * Список товаров в категории (с пагинацией).
     * GET /admin/catalog/category/{id}/products
     */
    public function categoryProducts(int $id, Request $request): JsonResponse
    {
        $page = $request->integer('page', 1);
        $perPage = $request->integer('per_page', 15);

        $list = $this->listProductByCategoryUseCase->execute($id, $perPage, $page);

        return response()->json($list, Response::HTTP_OK);
    }

    /**
     * Назначить товары категории (sync — заменяет весь набор).
     * POST /admin/catalog/category/{id}/products/sync
     */
    public function assignCategoryProducts(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $productIds = $request->input('products', []);

        $this->assignProductsToCategoryUseCase->execute($id, $productIds, $userPermission);

        return response()->json(['message' => 'Товары назначены'], Response::HTTP_OK);
    }

    /**
     * Добавить товары к категории (attach — дополняет существующие).
     * POST /admin/catalog/category/{id}/products/attach
     */
    public function attachCategoryProducts(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $productIds = $request->input('products', []);

        $this->attachProductToCategoryUseCase->execute($id, $productIds, $userPermission);

        return response()->json(['message' => 'Товары добавлены'], Response::HTTP_OK);
    }

    /**
     * Отвязать товары от категории.
     * DELETE /admin/catalog/category/{id}/products/detach
     */
    public function detachCategoryProducts(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $productIds = $request->input('products', []);

        $this->detachProductFromCategoryUseCase->execute($id, $productIds, $userPermission);

        return response()->json(['message' => 'Товары откреплены'], Response::HTTP_OK);
    }

    // ======================== Действия от товара ========================

    /**
     * Список категорий товара (массив).
     * GET /admin/catalog/product/{id}/categories
     */
    public function productCategories(int $id): JsonResponse
    {
        $list = $this->listCategoryByProductUseCase->execute($id);

        return response()->json($list, Response::HTTP_OK);
    }

    /**
     * Назначить категории товару (sync — заменяет весь набор).
     * POST /admin/catalog/product/{id}/categories/sync
     */
    public function assignProductCategories(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $categoryIds = $request->input('categories', []);

        $this->assignCategoriesToProductUseCase->execute($id, $categoryIds, $userPermission);

        return response()->json(['message' => 'Категории назначены'], Response::HTTP_OK);
    }

    /**
     * Добавить категории к товару (attach — дополняет существующие).
     * POST /admin/catalog/product/{id}/categories/attach
     */
    public function attachProductCategories(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $categoryIds = $request->input('categories', []);

        $this->attachCategoriesToProductUseCase->execute($id, $categoryIds, $userPermission);

        return response()->json(['message' => 'Категории добавлены'], Response::HTTP_OK);
    }

    /**
     * Отвязать категории от товара.
     * DELETE /admin/catalog/product/{id}/categories/detach
     */
    public function detachProductCategories(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $categoryIds = $request->input('categories', []);

        $this->detachCategoriesFromProductUseCase->execute($id, $categoryIds, $userPermission);

        return response()->json(['message' => 'Категории откреплены'], Response::HTTP_OK);
    }
}
