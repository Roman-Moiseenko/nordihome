<?php

namespace App\Modules\Shop\Application\DTOs;

use App\Modules\Catalog\Domain\ValueObjects\PriceType;

final readonly class ClientContext
{
    public function __construct(
        public ?int $id = null,
        public ?string $uuid = null,
        public string $priceType = PriceType::RETAIL,
    ) {}
}
