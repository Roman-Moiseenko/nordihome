<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoProduct;

use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductAttachData;
use App\Modules\Marketplace\Domain\Entities\AvitoProductEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class AttachProductToAvitoProductUseCase
{
    public function __construct(
        private AvitoProductRepositoryInterface $avitoProductRepository,
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(int $id, AvitoProductAttachData $dto, UserPermission $permission): AvitoProductEntity
    {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        $product = $this->productRepository->getById($dto->productId);

        $avitoProduct = $this->avitoProductRepository->getById($id);
        $avitoProduct->productId = $product->id;

        return $this->avitoProductRepository->save($avitoProduct);
    }
}
