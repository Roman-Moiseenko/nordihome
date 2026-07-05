<?php
declare(strict_types=1);

namespace App\Modules\Parser\Job;

use App\Modules\Catalog\Service\ProductService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Спарисание товара по артикулу и бренду
 */
class ParserProductByCode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private int $brand_id;
    private string $code;

    public function __construct(int $brand_id, string $code)
    {

        $this->brand_id = $brand_id;
        $this->code = $code;
    }

    public function handle(ProductService $service): void
    {
        \Log::info('Парсим товар ' . $this->code);
        try {
            $service->createByParser($this->brand_id, $this->code);
            \Log::info('Товар спарсился');
        } catch (\Throwable $e) {
            \Log::error($e->getMessage());
            \Log::error((string)$e->getLine());
            \Log::error($e->getFile());
        }

    }
}
