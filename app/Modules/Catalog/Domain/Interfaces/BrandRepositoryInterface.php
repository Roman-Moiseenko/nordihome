<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Interfaces;

use App\Modules\Catalog\Application\DTOs\Brand\FilterBrandIndexData;
use App\Modules\Catalog\Application\DTOs\Group\FilterGroupIndexData;
use App\Modules\Catalog\Domain\Entities\BrandEntity;
use Illuminate\Pagination\LengthAwarePaginator;

interface BrandRepositoryInterface
{
    /** @return BrandEntity[] */
    public function getAll(): array;

    public function getById(int $id): BrandEntity;

    public function save(BrandEntity $brand): BrandEntity;

    public function delete(int $id): void;

    public function hasProducts(int $id): bool;

    public function getByName(string $name): ?BrandEntity;

    /** @return BrandEntity[] */
    public function searchByName(string $name): array;

    public function getIkeaId(): int;

    public function getNbId(): int;
    public function filteredPaginated(FilterBrandIndexData $filter): LengthAwarePaginator;
}
