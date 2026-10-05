<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Series;

use App\Modules\Catalog\Domain\Interfaces\SeriesRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ListSeriesProductsQuery
{
    public function __construct(
        private SeriesRepositoryInterface $seriesRepository,
    )
    {
    }

    /**
     * @return array<int, array{id: int, code: string, name: string, category: string}>
     */
    public function execute(int $seriesId, UserPermission $userPermission): array
    {
        if (!$userPermission->can('catalog.product.view')) {
            throw new AccessDeniedException();
        }

        return $this->seriesRepository->getProducts($seriesId);
    }
}
