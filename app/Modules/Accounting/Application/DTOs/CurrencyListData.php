<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs;

use Spatie\LaravelData\Data;

class CurrencyListData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $sign,
    )
    {
    }
}
