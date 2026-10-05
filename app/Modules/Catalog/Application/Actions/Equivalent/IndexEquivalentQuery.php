<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Equivalent;

use App\Modules\Catalog\Application\DTOs\Equivalent\EquivalentIndexData;
use App\Modules\Catalog\Application\DTOs\Equivalent\FilterEquivalentIndexData;
use App\Modules\Catalog\Domain\Entities\EquivalentEntity;
use App\Modules\Catalog\Domain\Interfaces\CategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\EquivalentProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\EquivalentRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class IndexEquivalentQuery
{
    public function __construct(
        private EquivalentRepositoryInterface $equivalentRepository,
        private EquivalentProductRepositoryInterface $equivalentProductRepository,
        private CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function execute(FilterEquivalentIndexData &$filter, UserPermission $userPermission): LengthAwarePaginator
    {
        if (!$userPermission->can('catalog.product.view')) {
            throw new AccessDeniedException();
        }

        $paginator = $this->equivalentRepository->filteredPaginated($filter);

        $equivalentIds = $paginator->getCollection()
            ->map(fn(EquivalentEntity $equivalent) => $equivalent->id)
            ->toArray();

        $counts = $this->equivalentProductRepository->countProductsByEquivalentIds($equivalentIds);

        $categoryIds = $paginator->getCollection()
            ->map(fn(EquivalentEntity $equivalent) => $equivalent->categoryId)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $categories = [];
        if (!empty($categoryIds)) {
            $categories = collect($this->categoryRepository->findByIds($categoryIds))
                ->mapWithKeys(fn($category) => [$category->id => $category->name])
                ->toArray();
        }

        $dtos = $paginator->getCollection()->map(
            fn(EquivalentEntity $equivalent) => EquivalentIndexData::fromEntity(
                $equivalent,
                $categories[$equivalent->categoryId] ?? '',
                $counts[$equivalent->id] ?? 0
            )
        );
        $paginator->setCollection($dtos);

        return $paginator;
    }
}
