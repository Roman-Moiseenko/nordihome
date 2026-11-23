<?php

namespace App\Modules\Shop\Application\DTOs\PageElements;

readonly class MinMaxData
{
    public function __construct(
        public float $min,
        public float $max,
    )
    {
    }
}
