<?php

namespace App\Modules\Shop\Application\DTOs\Elements;

readonly class IkeaVariantData
{
    public function __construct(
        public int $id,
        public  string $name,
        public  string $code,
        public  string $image,

    )
    {
    }
}
