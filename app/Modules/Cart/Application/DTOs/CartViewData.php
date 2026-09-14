<?php

namespace App\Modules\Cart\Application\DTOs;

use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use Spatie\LaravelData\Data;

class CartViewData extends Data
{
    public function __construct(
        public readonly SeoData $meta,
    ){}
}
