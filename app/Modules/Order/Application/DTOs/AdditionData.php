<?php

namespace App\Modules\Order\Application\DTOs;

class AdditionData
{
    public function __construct(

        public float  $baseRatio,
        public string $name,
        public bool   $isQuantity,
        public bool   $isManual,
        public ?float $calculate,
        public int    $type,
    )
    {

    }
}
