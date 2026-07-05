<?php

namespace App\Modules\Parser\Infrastructure\Jobs;

use App\Modules\Parser\Application\Services\LoadParserProductIkeaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LoadProductsIkeaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly string $ikeaId)
    {
    }

    public function handle(LoadParserProductIkeaService $service): void
    {
        $service->GetListProductsByCategory($this->ikeaId);
    }
}
