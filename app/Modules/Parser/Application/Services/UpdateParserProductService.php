<?php

namespace App\Modules\Parser\Application\Services;

use App\Modules\Accounting\Application\Actions\PriceOutbox\CreatePriceOutboxUseCase;
use App\Modules\Accounting\Application\Actions\ProductPrice\CalculateRetailPriceUseCase;
use App\Modules\Accounting\Application\DTOs\PriceOutbox\PriceOutboxCreateData;
use App\Modules\Parser\Application\Actions\ParserLog\CreateParserLogUseCase;
use App\Modules\Parser\Application\Actions\Product\NewSellPriceParserProductUseCase;
use App\Modules\Parser\Application\DTOs\ParserLog\ParserLogCreateData;
use App\Modules\Parser\Domain\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Parser\Domain\ValueObjects\ParserStatus;
use App\Modules\Parser\Domain\ValueObjects\PriceChangePayload;

readonly class UpdateParserProductService
{

    public function __construct(
        private LoadParserProductIkeaService $service,
        private CreateParserLogUseCase       $createParserLogUseCase,
        private ParserProductRepositoryInterface $repository,
        private CalculateRetailPriceUseCase $calculateRetailPriceUseCase,
        private CreatePriceOutboxUseCase $createPriceOutboxUseCase,
        private NewSellPriceParserProductUseCase      $newSellPriceParserProductUseCase,

    )
    {
    }


    public function execute(int $productId): void
    {
        try {
            $productEntity = $this->repository->getById($productId);

            $resultUpdate = $this->service->UpdateParserProduct($productEntity);

            if (is_null($resultUpdate)) return; //Изменений нет
            $dto = new ParserLogCreateData(
                status: $resultUpdate->status,
                parserId: $productId,
            );

            //Для новых или изменееных цен
            if ($resultUpdate->status === ParserStatus::priceChanged()) {
                //Сохраняем новую цену и обновляем сущность
                $productEntity = $this->newSellPriceParserProductUseCase->execute($productEntity->id, $resultUpdate->newPrice);
                //Сохранить разницу цен для лога
                $payload = new PriceChangePayload($resultUpdate->previousPrice, $resultUpdate->newPrice);
                $dto->payload = $payload;

                //Для товаров которые есть в каталоге обновить цену для 1С
                if (!is_null($productEntity->productId)) {

                    $prices = $this->calculateRetailPriceUseCase->execute($productEntity);
                    $dtoOut = new PriceOutboxCreateData(
                        code: codeIkea($productEntity->code),
                        retail: $prices->retail,
                        sellIkea: $productEntity->priceSell,
                        bulk: $prices->bulk,
                    );
                    $this->createPriceOutboxUseCase->execute($dtoOut);
                }
            }
            //Для ненайденных, отключаем показ
            if ($resultUpdate->status === ParserStatus::deleted()) {
                $productEntity->availability = false;
                $this->repository->save($productEntity);
            }


        } catch (\Throwable $exception) {
            $error = $productId . ' ' .
                $exception->getMessage() . ' ' .
                $exception->getFile() . ' ' .
                $exception->getLine();

            $dto  = new ParserLogCreateData(
                status: ParserStatus::error(),
                parserId: $productId,
                error: $error,
            );
        }
        $this->createParserLogUseCase->execute($dto);
    }
}
