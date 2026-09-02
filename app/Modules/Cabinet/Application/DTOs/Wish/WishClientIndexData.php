<?php

namespace App\Modules\Cabinet\Application\DTOs\Wish;

use Spatie\LaravelData\Data;

class WishClientIndexData extends Data
{
    public function __construct(
        public int $id,
        public string $image,
        public string $name,
        public string $url,

    )
    {

    }
}
