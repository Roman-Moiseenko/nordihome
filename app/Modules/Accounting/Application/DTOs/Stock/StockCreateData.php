<?php

namespace App\Modules\Accounting\Application\DTOs\Stock;

use Spatie\LaravelData\Data;

class StockCreateData extends Data
{
public function __construct(
    public int $productId,
    public int $quantity,
    public int $reserve,
)
{

}
}
