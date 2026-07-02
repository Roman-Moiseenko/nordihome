<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Interfaces;

use App\Modules\Catalog\Application\DTOs\Category\CategoryProductData;
use App\Modules\Catalog\Application\DTOs\Product\ProductCategoryData;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryProductRepositoryInterface
{
    /**
     * Получить товары, привязанные к категории (с пагинацией).
     *
     * @param int $categoryId
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator<ProductCategoryData>
     */
    public function getProductsByCategoryId(int $categoryId, int $perPage = 15, int $page = 1): LengthAwarePaginator;

    /**
     * Получить категории, привязанные к товару (простой массив).
     *
     * @param int $productId
     * @return CategoryProductData[]
     */
    public function getCategoriesByProductId(int $productId): array;

    /**
     * Привязать товары к категории (добавление к существующим).
     *
     * @param int   $categoryId
     * @param int[] $productIds
     */
    public function attachProducts(int $categoryId, array $productIds): void;

    /**
     * Синхронизировать товары категории (заменить весь набор).
     *
     * @param int   $categoryId
     * @param int[] $productIds
     */
    public function syncProducts(int $categoryId, array $productIds): void;

    /**
     * Отвязать товары от категории.
     *
     * @param int   $categoryId
     * @param int[] $productIds
     */
    public function detachProducts(int $categoryId, array $productIds): void;

    /**
     * Привязать категории к товару (добавление к существующим).
     *
     * @param int   $productId
     * @param int[] $categoryIds
     */
    public function attachCategories(int $productId, array $categoryIds): void;

    /**
     * Синхронизировать категории товара (заменить весь набор).
     *
     * @param int   $productId
     * @param int[] $categoryIds
     */
    public function syncCategories(int $productId, array $categoryIds): void;

    /**
     * Отвязать категории от товара.
     *
     * @param int   $productId
     * @param int[] $categoryIds
     */
    public function detachCategories(int $productId, array $categoryIds): void;
}
