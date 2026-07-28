<?php

namespace App\Modules\Shop\Application\DTOs\Elements;

readonly class TableContent
{
    public function __construct(
        public string $id,
        public string $title,
    )
    {

    }
}
