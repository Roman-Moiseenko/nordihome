<?php

namespace App\Modules\Catalog\Infrastructure\Job;

use App\Modules\Catalog\Application\Services\SetSeriesToProductByNameService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use JetBrains\PhpStorm\Deprecated;

#[Deprecated]
class JobSetSeriesProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $productId,
        private readonly string $seriesName,
    )
    {
    }

    public function handle(SetSeriesToProductByNameService $service): void
    {
        try {
            $service->execute($this->productId, $this->seriesName);
        } catch (\Throwable $exception) {
            \Log::warning(json_encode([
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'productId' => $this->productId,
                'seriesName' => $this->seriesName,
            ]));
        }
    }
}
