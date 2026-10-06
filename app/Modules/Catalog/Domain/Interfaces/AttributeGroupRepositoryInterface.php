<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Interfaces;

use App\Modules\Catalog\Application\DTOs\AttributeGroup\AttributeGroupAttributeData;
use App\Modules\Catalog\Domain\Entities\AttributeGroupEntity;

interface AttributeGroupRepositoryInterface
{
    /**
     * @return AttributeGroupEntity[]
     */
    public function getAll(): array;

    public function getById(int $id): AttributeGroupEntity;

    /**
     * Имена групп по списку ID (без N+1).
     *
     * @param int[] $ids
     * @return array<int, string>
     */
    public function getNamesByIds(array $ids): array;

    public function save(AttributeGroupEntity $group): AttributeGroupEntity;

    public function delete(int $id): void;

    /**
     * @param int[] $ids
     * @return array<int, int>
     */
    public function countAttributesByGroupIds(array $ids): array;

    /**
     * Список атрибутов группы (без пагинации — атрибуты получаются через отношение).
     *
     * @return AttributeGroupAttributeData[]
     */
    public function getAttributes(int $groupId): array;

    public function hasAttributes(int $groupId): bool;

    /**
     * Обновить порядок сортировки группы.
     * Пересчитывает sort для остальных групп.
     */
    public function updateSortOrder(int $groupId, int $newSort): void;
}
