<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\Interfaces;

use App\Modules\Parser\Application\DTOs\Product\ParserProductFilterData;
use App\Modules\Parser\Domain\Entities\ParserProductEntity;
use Illuminate\Pagination\LengthAwarePaginator;


interface ParserProductRepositoryInterface
{
    public function getById(int $id): ParserProductEntity;

    public function getByCode(string $code): ?ParserProductEntity;

    public function save(ParserProductEntity $product): ParserProductEntity;

    public function delete(int $id): void;

    /** @return ParserProductEntity[] */
    public function getByCategoryIds(array $categoryIds): array;

    public function bulkToggleAvailability(array $productIds, bool $availability): void;

    public function findAllByCategoryId(int $categoryId, int $perPage = 15, int $page = 1): LengthAwarePaginator;

    /** @return array{products: LengthAwarePaginator, filters: array} */
    public function getFilteredPaginated(ParserProductFilterData $filter): LengthAwarePaginator;
}
