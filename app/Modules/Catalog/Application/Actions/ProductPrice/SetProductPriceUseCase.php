<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\ProductPrice;

use App\Modules\Catalog\Application\DTOs\ProductPrice\SetProductPriceData;
use App\Modules\Catalog\Application\Interfaces\ProductPriceRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\ProductPriceEntity;
use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class SetProductPriceUseCase
{
    public function __construct(
        private ProductPriceRepositoryInterface $priceRepository,
    )
    {
    }

    public function execute(SetProductPriceData $dto, UserPermission $userPermission): ProductPriceEntity
    {
        //if (!$userPermission->can('catalog.product.price.set')) throw new \DomainException('Доступ запрещён');


        $priceType = PriceType::fromString($dto->priceType);

        $price = new ProductPriceEntity(
            productId: $dto->productId,
            price: $dto->price,
            priceType: $priceType,
            setAt: new \DateTimeImmutable(),
        );

        $price->founded = $dto->founded;
        $price->comment = $dto->comment;

        return $this->priceRepository->save($price);
    }
}
