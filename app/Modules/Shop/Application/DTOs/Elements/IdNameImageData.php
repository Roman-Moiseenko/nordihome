<?php

namespace App\Modules\Shop\Application\DTOs\Elements;

readonly class IdNameImageData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $image,
    )
    {

    }
}
