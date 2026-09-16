<?php

namespace App\Modules\Accounting\Application\Services\Exchange;

use App\Modules\Accounting\Application\Actions\ProductPrice\SetProductPriceUseCase;
use App\Modules\Accounting\Application\DTOs\Exchange\PricePayloadData;
use App\Modules\Accounting\Application\DTOs\ProductPrice\SetProductPriceData;
use App\Modules\Accounting\Domain\ValueObjects\PriceType;
use App\Modules\Accounting\Infrastructure\Interfaces\ProductPriceRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;

readonly class LoadPriceProductsService
{

    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private SetProductPriceUseCase $priceUseCase,

    )
    {

    }
    public function execute(PricePayloadData $dto): bool
    {
        try {
            foreach ($dto->items as $item) {
                $product = $this->productRepository->findByCode($item->code);
                if (is_null($product)) {
                    //MAINDO Записываем в здровье сайта
                } else {
                    //Розничная
                    if (!is_null($item->retail)) {
                        $priceDto = new SetProductPriceData(
                            productId: $product->id,
                            price: $item->retail,
                            priceType: PriceType::RETAIL,
                            founded: 'Exchange1C',
                        );
                        $this->priceUseCase->execute($priceDto);
                    }
                    //Оптовая
                    if (!is_null($item->bulk)) {
                        $priceDto = new SetProductPriceData(
                            productId: $product->id,
                            price: $item->bulk,
                            priceType: PriceType::BULK,
                            founded: 'Exchange1C',
                        );
                        $this->priceUseCase->execute($priceDto);
                    }
                    //Специальная
                    if (!is_null($item->special)) {
                        $priceDto = new SetProductPriceData(
                            productId: $product->id,
                            price: $item->special,
                            priceType: PriceType::SPECIAL,
                            founded: 'Exchange1C',
                        );
                        $this->priceUseCase->execute($priceDto);
                    }
                    //Минимальная
                    if (!is_null($item->minimal)) {
                        $priceDto = new SetProductPriceData(
                            productId: $product->id,
                            price: $item->minimal,
                            priceType: PriceType::MINIMAL,
                            founded: 'Exchange1C',
                        );
                        $this->priceUseCase->execute($priceDto);
                    }
                    //Себестоимость
                    if (!is_null($item->cost)) {
                        $priceDto = new SetProductPriceData(
                            productId: $product->id,
                            price: $item->cost,
                            priceType: PriceType::COST,
                            founded: 'Exchange1C',
                        );
                        $this->priceUseCase->execute($priceDto);
                    }

                }
            }

        } catch (\Throwable $e) {
            \Log::warning(json_encode([$e->getMessage(), $e->getFile(), $e->getLine()]));
            //Записываем в здровье сайта
            return false;
        }
        return true;
    }
}
