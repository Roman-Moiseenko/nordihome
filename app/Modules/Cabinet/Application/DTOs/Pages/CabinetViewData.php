<?php

namespace App\Modules\Cabinet\Application\DTOs\Pages;

use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use Spatie\LaravelData\Data;

class CabinetViewData extends Data
{
    public function __construct(
        public readonly SeoData $meta,
    ){}
}
