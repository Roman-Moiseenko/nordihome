<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Series;

use App\Modules\Catalog\Application\DTOs\Series\FilterSeriesIndexData;
use App\Modules\Catalog\Application\DTOs\Series\SeriesIndexData;
use App\Modules\Catalog\Domain\Entities\SeriesEntity;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\SeriesRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class IndexSeriesQuery
{
    public function __construct(
        private SeriesRepositoryInterface $seriesRepository,
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(FilterSeriesIndexData &$filter, UserPermission $userPermission): LengthAwarePaginator
    {
        if (!$userPermission->can('catalog.product.view')) {
            throw new AccessDeniedException();
        }

        $paginator = $this->seriesRepository->filteredPaginated($filter);

        $seriesIds = $paginator->getCollection()
            ->map(fn(SeriesEntity $series) => $series->id)
            ->toArray();

        $counts = $this->productRepository->countProductsBySeriesIds($seriesIds);

        $dtos = $paginator->getCollection()->map(
            fn(SeriesEntity $series) => SeriesIndexData::fromEntity(
                $series,
                $counts[$series->id] ?? 0
            )
        );
        $paginator->setCollection($dtos);

        return $paginator;
    }
}
