<?php

namespace App\Jobs;

use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Parser\ParserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Tests\CreatesApplication;

class ParserProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $code;

    /**
     * Create a new job instance.
     */
    public function __construct(string $code)
    {
        $this->code = $code;
    }

    /**
     * Execute the job.
     */
    public function handle(ParserService $service): void
    {
        /** @var Product $product */
        $product = Product::where('code_search', $this->code)->first();

        if (is_null($product)) { //Новый товар
            $service->findProduct($this->code);
        } else {
            //Если нет данных о размерах
            if (is_null($product->dimensions) || ($product->dimensions->height == 0)) {
                $service->findProduct($this->code);
            }

            if ($product->isPublished() && !$product->isSale()) { //Опубликован, но снят с продажи,
                $product->setForSale(); // появился снова.
            }

        }


    }
}
