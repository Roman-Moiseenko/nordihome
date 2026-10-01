<?php

namespace App\Modules\Parser\Infrastructure\Jobs;

use App\Modules\Parser\Application\Actions\ParserLog\CreateParserLogUseCase;
use App\Modules\Parser\Application\DTOs\ParserLog\ParserLogCreateData;
use App\Modules\Parser\Application\Services\LoadParserProductIkeaService;
use App\Modules\Parser\Domain\Entities\ParserProductEntity;
use App\Modules\Parser\Domain\ValueObjects\ParserStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use function Laravel\Prompts\warning;

class RenameIkeaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly ParserProductEntity $entity)
    {
    }

    public function handle(
        LoadParserProductIkeaService $service,
    ): void
    {
        try {
            $service->RenameParserProduct($this->entity);
        } catch (\Throwable $e) {
            \Log::warning(json_encode([
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
            ]));
        }

    }
}
