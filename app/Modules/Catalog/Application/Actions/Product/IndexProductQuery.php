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

        // При show=delete репозиторий применяет onlyTrashed(), т.е. все
        // товары страницы — удалённые.
        $trashed = $filter->show === 'delete';

        $entities = $paginator->getCollection();

        // При groupModification=true вторичные товары модификации исключены
        // из списка на уровне репозитория. Здесь подгружаем их и вкладываем
        // в первичный товар в поле $modifications.
        $secondaryMap = [];
        $secondaryById = [];

        if ($filter->groupModification) {
            $primaryIds = $entities
                ->map(fn(ProductEntity $product) => $product->id)
                ->filter(fn(?int $id) => $id !== null && $id > 0)
                ->values()
                ->toArray();

            $secondaryMap = $this->productRepository->findModificationSecondaryProductIds($primaryIds);

            $secondaryIds = [];
            foreach ($secondaryMap as $ids) {
                $secondaryIds = array_merge($secondaryIds, $ids);
            }
            $secondaryIds = array_values(array_unique($secondaryIds));

            if (!empty($secondaryIds)) {
                $secondaryEntities = $this->productRepository->findByIds($secondaryIds, $trashed);
                foreach ($secondaryEntities as $secondaryEntity) {
                    $secondaryById[$secondaryEntity->id] = $secondaryEntity;
                }
            }
        }

        // Имена родительских категорий для товаров страницы и вложенных
        // товаров модификаций (одним запросом).
        $categoryIds = $entities
            ->map(fn(ProductEntity $product) => $product->mainCategoryId)
            ->filter(fn(?int $id) => $id !== null && $id > 0);

        foreach ($secondaryById as $secondaryEntity) {
            if ($secondaryEntity->mainCategoryId > 0) {
                $categoryIds->push($secondaryEntity->mainCategoryId);
            }
        }

        $categoryNames = $this->categoryRepository->getParentNamesByIds(
            $categoryIds->unique()->values()->toArray()
        );

        $paginator->setCollection(
            $entities->map(
                function (ProductEntity $product) use ($categoryNames, $trashed, $secondaryMap, $secondaryById) {
                    $modifications = [];

                    foreach ($secondaryMap[$product->id] ?? [] as $secondaryId) {
                        if (!isset($secondaryById[$secondaryId])) {
                            continue;
                        }

                        $secondary = $secondaryById[$secondaryId];
                        $modifications[] = ProductIndexData::fromEntity(
                            $secondary,
                            $categoryNames[$secondary->mainCategoryId] ?? '',
                            $trashed,
                        );
                    }

                    return ProductIndexData::fromEntity(
                        $product,
                        $categoryNames[$product->mainCategoryId] ?? '',
                        $trashed,
                        $modifications,
                    );
                }
            )
        );

        return $paginator;
    }
}
