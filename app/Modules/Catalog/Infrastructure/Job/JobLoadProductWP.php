<?php

namespace App\Modules\Catalog\Infrastructure\Job;

use App\Modules\Catalog\Application\Services\LoadCategoryWpService;
use App\Modules\Catalog\Application\Services\LoadProductWpService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobLoadProductWP implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly array $product)
    {
    }

    public function handle(LoadProductWpService $loadProductWpService): void
    {
        try {
            if (!$loadProductWpService->load($this->product))
                \Log::info('Не загружен ' . $this->product['sku']);

        } catch (\Throwable $exception) {

            $message = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'product' => $this->product,
            ];
            \Log::info(json_encode($message));
        }

    }
}
