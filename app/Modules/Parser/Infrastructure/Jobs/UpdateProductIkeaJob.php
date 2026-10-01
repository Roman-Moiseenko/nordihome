<?php

namespace App\Modules\Parser\Infrastructure\Jobs;

use App\Modules\Accounting\Application\Actions\PriceOutbox\CreatePriceOutboxUseCase;
use App\Modules\Accounting\Application\Actions\ProductPrice\CalculateRetailPriceUseCase;
use App\Modules\Accounting\Application\DTOs\PriceOutbox\PriceOutboxCreateData;
use App\Modules\Parser\Application\Actions\ParserLog\CreateParserLogUseCase;
use App\Modules\Parser\Application\DTOs\ParserLog\ParserLogCreateData;
use App\Modules\Parser\Application\Services\LoadParserProductIkeaService;
use App\Modules\Parser\Domain\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Parser\Domain\ValueObjects\ParserStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateProductIkeaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private int $productId)
    {
    }

    public function handle(
        LoadParserProductIkeaService $service,
        CreateParserLogUseCase       $createParserLogUseCase,
        ParserProductRepositoryInterface $repository,
        CalculateRetailPriceUseCase $calculateRetailPriceUseCase,
        CreatePriceOutboxUseCase $createPriceOutboxUseCase,
    ): void
    {
        try {
            $status = $service->UpdateParserProduct($this->productId);
            if (is_null($status)) return; //Изменений нет

            $dto = new ParserLogCreateData(
                status: $status,
                parserId: $this->productId,
            );

            if ($status === ParserStatus::priceChanged()) {

                $entity = $repository->getById($this->productId);
                if (!is_null($entity->productId)) {
                    $dtoOut = new PriceOutboxCreateData(
                        code: codeIkea($entity->code),
                        price: $calculateRetailPriceUseCase->execute($entity->priceSell),
                        priceIkea: $entity->priceSell
                    );
                    $createPriceOutboxUseCase->execute($dtoOut);
                }
            }


        } catch (\Throwable $exception) {
            $error = $this->productId . ' ' .
                $exception->getMessage() . ' ' .
                $exception->getFile() . ' ' .
                $exception->getLine();

            $dto  = new ParserLogCreateData(
                status: ParserStatus::error(),
                error: $error,
            );
        }
        $createParserLogUseCase->execute($dto);
    }
}
