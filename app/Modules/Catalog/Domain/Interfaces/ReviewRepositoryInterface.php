<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Interfaces;

use App\Modules\Catalog\Application\DTOs\Review\FilterReviewIndexData;
use App\Modules\Catalog\Domain\Entities\ReviewEntity;
use Illuminate\Pagination\LengthAwarePaginator;

interface ReviewRepositoryInterface
{
    /** @return LengthAwarePaginator<ReviewEntity> */
    public function getFilteredPaginated(FilterReviewIndexData $filter): LengthAwarePaginator;

    /** @return LengthAwarePaginator<ReviewEntity> */
    public function getByProductIdPaginated(int $productId, int $perPage = 15, int $page = 1): LengthAwarePaginator;

    /** @return LengthAwarePaginator<ReviewEntity> */
    public function getByClientIdPaginated(int $clientId, int $perPage = 15, int $page = 1): LengthAwarePaginator;

    public function getById(int $id): ReviewEntity;

    public function save(ReviewEntity $review): ReviewEntity;
}
