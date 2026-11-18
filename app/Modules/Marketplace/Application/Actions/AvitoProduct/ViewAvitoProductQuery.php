<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoProduct;

use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductViewData;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryRepositoryInterface;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ViewAvitoProductQuery
{
    public function __construct(
        private AvitoProductRepositoryInterface $avitoProductRepository,
        private AvitoCategoryRepositoryInterface $avitoCategoryRepository,
        private ProductRepositoryInterface $productRepository,
        private CalculateAvitoSellPriceUseCase $calculateAvitoSellPriceUseCase,
    ) {}

    public function execute(int $id, UserPermission $permission): AvitoProductViewData
    {
        if (!$permission->can('marketplace.avito.view')) {
            throw new AccessDeniedException();
        }

        $entity = $this->avitoProductRepository->getById($id);

        $productCode = null;
        if ($entity->productId !== null) {
            foreach ($this->productRepository->findByIds([$entity->productId]) as $product) {
                if ($product->id === $entity->productId) {
                    $productCode = (string) $product->code;
                    break;
                }
            }
        }

        $categoryPath = '';
        if ($entity->avitoCategoryId !== null) {
            $paths = $this->avitoCategoryRepository->getPathNamesByIds([$entity->avitoCategoryId]);
            $categoryPath = $paths[$entity->avitoCategoryId] ?? '';
        }

        return AvitoProductViewData::fromEntity(
            $entity,
            $productCode,
            $categoryPath,
            $this->calculateAvitoSellPriceUseCase->withDelivery($entity),
            $this->calculateAvitoSellPriceUseCase->withoutDelivery($entity),
        );
    }
}
