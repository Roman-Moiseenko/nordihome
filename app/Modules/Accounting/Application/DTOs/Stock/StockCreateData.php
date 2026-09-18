<?php

namespace App\Modules\Accounting\Application\DTOs\Stock;

use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class StockCreateData extends Data
{
public function __construct(
    #[Required, Numeric]
    public int $productId,
    #[Required, Numeric, Min(0)]
    public int $quantity,
    #[Nullable, Numeric]
    public ?int $reserve = 0,
)
{

}
}
