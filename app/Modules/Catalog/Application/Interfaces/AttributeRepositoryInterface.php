<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Interfaces;

use App\Modules\Catalog\Application\DTOs\Attribute\AttributeCategoryData;

interface AttributeRepositoryInterface
{
    /**
     * Получить атрибуты для категории, сгруппированные по принадлежности:
     * - self: атрибуты, привязанные напрямую к этой категории
     * - parent: атрибуты, привязанные к родительским категориям
     *
     * @param int $categoryId
     * @return array{
     *     self: AttributeCategoryData[],
     *     parent: AttributeCategoryData[]
     * }
     */
    public function findForCategory(int $categoryId): array;
}
