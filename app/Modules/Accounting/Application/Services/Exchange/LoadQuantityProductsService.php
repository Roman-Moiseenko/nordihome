<?php

namespace App\Modules\Accounting\Application\Services\Exchange;

use App\Modules\Accounting\Application\Actions\Stock\AddProductToStockUseCase;
use App\Modules\Accounting\Application\DTOs\Exchange\StockPayloadData;
use App\Modules\Accounting\Application\DTOs\Stock\StockCreateData;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;

readonly class LoadQuantityProductsService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private AddProductToStockUseCase   $toStockUseCase,

    )
    {

    }
    public function execute(StockPayloadData $dto): bool
    {
        try {

            foreach ($dto->items as $item) {
                $product = $this->productRepository->findByCode($item->code);
                if (is_null($product)) {
                    //MAINDO Записываем в здровье сайта
                } else {
                    //TODO Сделать через Job
                    $dto = new StockCreateData(
                        productId: $product->id,
                        quantity: $item->quantity,
                        reserve: $item->reserve,
                    );
                    $this->toStockUseCase->execute($dto);
                }
            }
        } catch (\Throwable $exception) {
            \Log::warning($exception->getMessage());
            //Записываем в здровье сайта
            return false;
        }
        return true;
    }
}
