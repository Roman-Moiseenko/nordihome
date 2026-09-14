<?php

namespace App\Modules\Cabinet\Application\DTOs\Pages;

use App\Modules\Cabinet\Application\DTOs\ReviewClientData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use Spatie\LaravelData\Data;

class ReviewViewData extends Data
{
    public function __construct(
        public readonly SeoData $meta,
        /** @var ReviewClientData[] $reviews */
        public readonly array $reviews = [],
    ){}
}
