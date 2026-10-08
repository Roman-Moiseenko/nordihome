<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Interfaces;

use App\Modules\Catalog\Application\DTOs\Equivalent\FilterEquivalentIndexData;
use App\Modules\Catalog\Domain\Entities\EquivalentEntity;
use Illuminate\Pagination\LengthAwarePaginator;

interface EquivalentRepositoryInterface
{
    public function getById(int $id): EquivalentEntity;

    /**
     * Полный список групп аналогов (для панели товара).
     *
     * @return array<int, array{id: int, name: string}>
     */
    public function getAll(): array;

    /**
     * @param int[] $ids
     * @return EquivalentEntity[]
     */
    public function findByIds(array $ids): array;

    public function save(EquivalentEntity $equivalent): EquivalentEntity;

    public function delete(int $id): void;

    public function filteredPaginated(FilterEquivalentIndexData &$filter): LengthAwarePaginator;
}
