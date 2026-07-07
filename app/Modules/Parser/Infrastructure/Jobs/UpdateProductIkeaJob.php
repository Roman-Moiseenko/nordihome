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

class UpdateProductIkeaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private int $productId)
    {
    }

    public function handle(
        LoadParserProductIkeaService $service,
        CreateParserLogUseCase       $createParserLogUseCase,
    ): void
    {
        try {
            $status = $service->UpdateParserProduct($this->productId);
            if (is_null($status)) return; //Изменений неи

            $dto = new ParserLogCreateData(
                status: $status,
                parserId: $this->productId,
            );

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
