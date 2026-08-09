<?php

namespace App\Modules\Order\Application\DTOs;

class ProductItemData
{
    public function __construct(
        public int     $id,
        public string  $name,
        public string  $code,
        public string  $volume,
        public string  $weight,
    )
    {

    }
}
