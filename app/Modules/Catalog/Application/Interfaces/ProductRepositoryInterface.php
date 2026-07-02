<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Interfaces;

use App\Modules\Catalog\Application\DTOs\Product\ProductCategoryData;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    /**
     * Найти товары по ID категории (основной категории).
     * Исключаются модификации (через has('main_modification') / doesntHave('modification')).
     *
     * @param int $categoryId
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator<ProductCategoryData>
     */
    public function findByMainCategoryId(int $categoryId, int $perPage = 15, int $page = 1): LengthAwarePaginator;

    /**
     * Найти все товары категории (основная + привязанные через pivot).
     * Объединённый список без дубликатов по ID товара.
     * Исключаются модификации.
     *
     * @param int $categoryId
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator<ProductCategoryData>
     */
    public function findAllByCategoryId(int $categoryId, int $perPage = 15, int $page = 1): LengthAwarePaginator;
}

