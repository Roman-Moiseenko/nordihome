<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoProduct;

use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductIndexData;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\FilterAvitoProductIndexData;
use App\Modules\Marketplace\Domain\Entities\AvitoProductEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryRepositoryInterface;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class IndexAvitoProductQuery
{
    public function __construct(
        private AvitoProductRepositoryInterface $avitoProductRepository,
        private AvitoCategoryRepositoryInterface $avitoCategoryRepository,
        private ProductRepositoryInterface $productRepository,
    ) {}

    /**
     * @return LengthAwarePaginator<AvitoProductIndexData>
     */
    public function execute(FilterAvitoProductIndexData &$filter, UserPermission $permission): LengthAwarePaginator
    {
        if (!$permission->can('marketplace.avito.view')) {
            throw new AccessDeniedException();
        }

        $paginator = $this->avitoProductRepository->getFilteredPaginated($filter);

        /** @var AvitoProductEntity[] $entities */
        $entities = $paginator->getCollection()->all();

        $productIds = array_values(array_filter(array_map(
            fn(AvitoProductEntity $entity) => $entity->productId,
            $entities
        )));

        $categoryIds = array_values(array_filter(array_map(
            fn(AvitoProductEntity $entity) => $entity->avitoCategoryId,
            $entities
        )));

        $codes = [];
        foreach ($this->productRepository->findByIds($productIds) as $product) {
            $codes[$product->id] = (string) $product->code;
        }

        $paths = $this->avitoCategoryRepository->getPathNamesByIds($categoryIds);

        return $paginator->through(function (AvitoProductEntity $entity) use ($codes, $paths) {
            return new AvitoProductIndexData(
                id: $entity->id,
                avitoId: $entity->avitoId,
                name: $entity->name,
                code: $entity->productId !== null ? ($codes[$entity->productId] ?? null) : null,
                categoryPath: $entity->avitoCategoryId !== null
                    ? ($paths[$entity->avitoCategoryId] ?? '')
                    : '',
                delivery: $entity->delivery,
                active: $entity->active,
                tnVed: $entity->tnVed,
            );
        });
    }
}
