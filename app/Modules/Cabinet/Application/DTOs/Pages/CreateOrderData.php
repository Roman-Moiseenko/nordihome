<?php

namespace App\Modules\Cabinet\Application\DTOs\Pages;

use App\Modules\Cart\Application\DTOs\CartInfoData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use Spatie\LaravelData\Data;

class CreateOrderData extends Data
{
    public function __construct(
        public readonly SeoData $meta,
        public readonly CartInfoData $cartInfo,
    ){}
}
