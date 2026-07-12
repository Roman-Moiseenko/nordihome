<?php

namespace App\Modules\Shop\Application\DTOs\Elements;

readonly class DimensionsData
{
    public function __construct(
        public float $height,
        public float $width,
        public float $depth,
        public float $weight,
        public float $volume,
        public array $captions,
    ){}
}
