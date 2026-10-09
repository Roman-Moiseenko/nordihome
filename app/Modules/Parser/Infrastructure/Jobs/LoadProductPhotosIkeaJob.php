<?php

namespace App\Modules\Parser\Infrastructure\Jobs;

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

class LoadProductPhotosIkeaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly int $productId)
    {
    }

    public function handle(
        LoadParserProductIkeaService $service,
        ParserProductRepositoryInterface $repository,
        CreateParserLogUseCase $createParserLogUseCase,
    ): void
    {
        $entity = $repository->getById($this->productId);
        try {
            $service->parsePhotos($this->productId, $entity->code);
          //  \Log::info('Задача отработана ' . $this->productId);
        } catch (\Throwable $exception) {
            $error = 'LoadProductPhotos ('. $entity->code . ') - ' .
                $exception->getMessage() . ' ' .
                $exception->getFile() . ' ' .
                $exception->getLine();

            \Log::warning($error);
            $dto  = new ParserLogCreateData(
                status: ParserStatus::error(),
                error: $error,
            );
            $createParserLogUseCase->execute($dto);
        }

    }
}
