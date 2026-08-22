<?php

namespace App\Modules\Order\Application\DTOs;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class OrderAddProductData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public int $productId,
        #[Required, Numeric]
        public int $quantity,
        #[Nullable, BooleanType]
        public ?bool $preorder = false,
        #[Nullable, BooleanType]
        public ?bool $increase = false, //Увеличение имеющегося (true) или добавление новой позиции (false)
    ) {}
}
