<?php

namespace App\Modules\Content\Entity\Widgets;

readonly class DTOProductWidget
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public float $price,
    )
    {

    }
}
