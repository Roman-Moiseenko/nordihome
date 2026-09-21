<?php

namespace App\Modules\Cabinet\Application\DTOs\Pages;

use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use Spatie\LaravelData\Data;

class NewOrderData extends Data
{
    public function __construct(
        public readonly SeoData $meta,
        public readonly string $numberOrder,
        public readonly string $dateOrder,
        public readonly array $eArray,
    ){}
}
