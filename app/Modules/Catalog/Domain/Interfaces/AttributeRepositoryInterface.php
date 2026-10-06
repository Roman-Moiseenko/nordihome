<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Interfaces;

use App\Modules\Catalog\Application\DTOs\Attribute\AttributeCategoryData;
use App\Modules\Catalog\Application\DTOs\Attribute\FilterAttributeIndexData;
use App\Modules\Catalog\Domain\Entities\AttributeEntity;
use Illuminate\Pagination\LengthAwarePaginator;

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

    /**
     * Атрибут по id вместе с вариантами.
     */
    public function getById(int $id): AttributeEntity;

    /**
     * Список атрибутов с пагинацией и фильтрацией.
     *
     * @return LengthAwarePaginator<AttributeEntity>
     */
    public function getFilteredPaginated(FilterAttributeIndexData &$filter): LengthAwarePaginator;

    /**
     * Сохранить атрибут и его варианты (создание/обновление/удаление вариантов).
     */
    public function save(AttributeEntity $attribute): AttributeEntity;

    public function delete(int $id): void;
}
