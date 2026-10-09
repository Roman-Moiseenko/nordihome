<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product;

use App\Modules\Catalog\Application\DTOs\Product\FilterProductIndexData;
use App\Modules\Catalog\Application\DTOs\Product\ProductIndexData;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\Interfaces\CategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Список товаров (Catalog/Product/Index) с фильтрацией и пагинацией.
 *
 * Следует потоку данных из INSTRUCTION.md (раздел 12):
 * Filter DTO передаётся по ссылке, репозиторий пишет в него $count.
 *
 * @return LengthAwarePaginator<ProductIndexData>
 */
readonly class IndexProductQuery
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function execute(FilterProductIndexData &$filter, UserPermission $userPermission): LengthAwarePaginator
    {
        if (!$userPermission->can('catalog.product.view')) {
            throw new AccessDeniedException();
        }

        $paginator = $this->productRepository->filteredPaginated($filter);

        // Имена родительских категорий для всех товаров страницы (одним запросом).
        $categoryIds = $paginator->getCollection()
            ->map(fn(ProductEntity $product) => $product->mainCategoryId)
            ->filter(fn(?int $id) => $id !== null && $id > 0)
            ->unique()
            ->values()
            ->toArray();

        $categoryNames = $this->categoryRepository->getParentNamesByIds($categoryIds);

        // При show=delete репозиторий применяет onlyTrashed(), т.е. все
        // товары страницы — удалённые.
        $trashed = $filter->show === 'delete';

        $paginator->setCollection(
            $paginator->getCollection()->map(
                fn(ProductEntity $product) => ProductIndexData::fromEntity(
                    $product,
                    $categoryNames[$product->mainCategoryId] ?? '',
                    $trashed,
                )
            )
        );

        return $paginator;
    }
}
