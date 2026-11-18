<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoProduct;

use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductUpdateData;
use App\Modules\Marketplace\Domain\Entities\AvitoProductEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdateAvitoProductUseCase
{
    public function __construct(
        private AvitoProductRepositoryInterface $avitoProductRepository,
    ) {}

    public function execute(int $id, AvitoProductUpdateData $dto, UserPermission $permission): AvitoProductEntity
    {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        $product = $this->avitoProductRepository->getById($id);

        $product->name = $dto->name;
        $product->basePrice = $dto->basePrice ?? 0;
        $product->sellPrice = $dto->sellPrice;
        $product->pricePackage = $dto->pricePackage ?? 0;
        $product->tnVed = $dto->tnVed;
        $product->okpd2 = $dto->okpd2;
        $product->country = $dto->country;
        $product->delivery = $dto->delivery;
        $product->inStock = $dto->inStock;
        $product->active = $dto->active;
        $product->sale = $dto->sale;

        return $this->avitoProductRepository->save($product);
    }
}
