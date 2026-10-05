<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Interfaces;

use App\Modules\Catalog\Application\DTOs\Group\FilterGroupIndexData;
use App\Modules\Catalog\Domain\Entities\GroupEntity;
use Illuminate\Pagination\LengthAwarePaginator;

interface GroupRepositoryInterface
{
    public function getById(int $id): GroupEntity;

    /**
     * @return GroupEntity[]
     */
    public function findByProductId(int $productId): array;

    public function getAllPaginated(int $perPage = 15, int $page = 1): LengthAwarePaginator;

    public function save(GroupEntity $group): GroupEntity;

    public function delete(int $id): void;

    public function existsSlug(string $slug, ?int $excludeId = null): bool;

    /**
     * @return LengthAwarePaginator<GroupEntity>
     */
    public function getFilteredPaginated(FilterGroupIndexData &$filter): LengthAwarePaginator;

    /**
     * @return GroupEntity[]
     */
    public function getAll(): array;
}
