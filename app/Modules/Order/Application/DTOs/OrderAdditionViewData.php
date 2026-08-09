<?php

namespace App\Modules\Order\Application\DTOs;

readonly class OrderAdditionViewData
{
    public function __construct(
        public int $id,
        /**
         * @var float $amount передаем либо значение из базы, либо вычисляемое getAmount(),
         */
        public float $amount,
        public ?string $comment,
        public ?int $quantity,

        public AdditionData $addition,

    )
    {

    }
}
