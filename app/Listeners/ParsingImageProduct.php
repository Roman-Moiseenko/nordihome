<?php

namespace App\Listeners;

use App\Events\ProductHasParsed;
use App\Modules\Base\Job\LoadingImageProduct;
use App\Modules\Shop\Parser\ParserService;
use App\Modules\Shop\Parser\ProductParser;


class ParsingImageProduct
{
    private ParserService $service;

    public function __construct(ParserService $service)
    {
        $this->service = $service;
    }

    public function handle(ProductHasParsed $event): void
    {
        /** @var ProductParser $productParser */
        $productParser = ProductParser::where('product_id', $event->product->id)->first();
        $images = $this->service->parserImage($productParser->link);
        foreach ($images as $image_url) {
            LoadingImageProduct::dispatch($event->product, $image_url, '', true);
        }
    }
}
