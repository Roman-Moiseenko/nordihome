<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Brand;

use App\Modules\Catalog\Application\DTOs\Brand\BrandIndexData;
use App\Modules\Catalog\Application\DTOs\Brand\FilterBrandIndexData;
use App\Modules\Catalog\Domain\Entities\BrandEntity;
use App\Modules\Catalog\Domain\Interfaces\BrandRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class IndexBrandQuery
{
    public function __construct(
        private BrandRepositoryInterface $brandRepository,
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(FilterBrandIndexData &$filter, UserPermission $userPermission): LengthAwarePaginator
    {
        if (!$userPermission->can('catalog.product.view')) throw new AccessDeniedException();


        $paginator = $this->brandRepository->filteredPaginated($filter);
        $brandIds = $paginator->getCollection()
            ->map(fn (BrandEntity $brand) => $brand->id)
            ->toArray();
        $counts = $this->productRepository->countProductsByBrandIds($brandIds);

        // 4. Преобразование в DTO с количеством
        $dtos = $paginator->getCollection()->map(
            fn (BrandEntity $brand) => BrandIndexData::fromEntity(
                $brand,
                $counts[$brand->id] ?? 0
            )
        );
        $paginator->setCollection($dtos);

        return $paginator;

    }
}
