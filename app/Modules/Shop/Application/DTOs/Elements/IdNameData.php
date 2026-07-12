<?php

namespace App\Modules\Shop\Application\DTOs\Elements;

final readonly class IdNameData
{
    public function __construct(
        public int    $id,
        public string $name,
    )
    {

    }
}
