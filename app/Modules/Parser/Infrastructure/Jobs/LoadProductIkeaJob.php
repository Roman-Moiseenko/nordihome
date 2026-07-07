<?php

namespace App\Modules\Parser\Infrastructure\Jobs;

use App\Modules\Parser\Application\Actions\ParserLog\CreateParserLogUseCase;
use App\Modules\Parser\Application\DTOs\ParserLog\ParserLogCreateData;
use App\Modules\Parser\Application\Services\LoadParserProductIkeaService;
use App\Modules\Parser\Domain\ValueObjects\ParserStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LoadProductIkeaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly array $productData)
    {
    }

    public function handle(
        LoadParserProductIkeaService $service,
        CreateParserLogUseCase $createParserLogUseCase,
    ): void
    {
        //\Log::warning(json_encode($this->productData));
        try {
            $entity = $service->CreateParserProduct($this->productData);
            if (is_null($entity)) return; //Товар уже был спарсен ранее
            $dto = new ParserLogCreateData(
                status: ParserStatus::new(),
                parserId: $entity->id,
            );

        } catch (\Throwable $exception) {
            $error = $this->productData['itemNoGlobal'] . ' ' .
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
