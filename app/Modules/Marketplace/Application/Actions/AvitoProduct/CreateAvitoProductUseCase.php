<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoProduct;

use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductCreateData;
use App\Modules\Marketplace\Domain\Entities\AvitoProductEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class CreateAvitoProductUseCase
{
    public function __construct(
        private AvitoProductRepositoryInterface $avitoProductRepository,
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(AvitoProductCreateData $dto, UserPermission $permission): AvitoProductEntity
    {
        if (!$permission->can('marketplace.avito.create')) {
            throw new AccessDeniedException();
        }

        $product = $this->productRepository->getById($dto->productId);

        $avitoProduct = new AvitoProductEntity(
            name: $product->name,
            productId: $product->id,
        );

        // По умолчанию доставка отключена и объявление не активно.
        $avitoProduct->delivery = false;
        $avitoProduct->active = false;

        return $this->avitoProductRepository->save($avitoProduct);
    }
}
