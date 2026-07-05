<?php

namespace App\Modules\Parser\Infrastructure\Jobs;

use App\Modules\Parser\Application\Services\LoadParserProductIkeaService;
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

    public function handle(LoadParserProductIkeaService $service): void
    {
        $entity = $service->CreateParserProduct($this->productData);
        //MAINDO Логи сделать ParserLog
    }
}
