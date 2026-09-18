<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\ProductPrice;

use App\Modules\Accounting\Application\DTOs\ProductPrice\SetProductPriceData;
use App\Modules\Accounting\Domain\Entities\ProductPriceEntity;
use App\Modules\Accounting\Domain\ValueObjects\PriceType;
use App\Modules\Accounting\Infrastructure\Interfaces\PriceRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class SetPriceUseCase
{
    public function __construct(
        private PriceRepositoryInterface $priceRepository,
    )
    {
    }

    public function execute(SetProductPriceData $dto): ProductPriceEntity
    {
        $priceType = PriceType::fromString($dto->priceType);

        //Если цена не изменилась, то не сохраняем
        $lastPrice = $this->priceRepository->getLastByProductAndType($dto->productId, $dto->priceType);
        if (!is_null($lastPrice)) {
            if ($lastPrice->price == $dto->price) return $lastPrice;
        }

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
