<?php

namespace App\Modules\Parser\Application\DTOs\Product;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class ParserProductUpdateData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $name = null,
        public readonly ?string $code = null,
        public readonly ?string $slug = null,
        public readonly ?string $url = null,
        public readonly ?float $priceSell = null,
        public readonly ?float $priceBase = null,
        public readonly ?string $short = null,
        public readonly ?string $description = null,
        public readonly ?bool $fragile = null,
        public readonly ?bool $sanctioned = null,
        public readonly ?bool $availability = null,
        /** @var array[]|null $packages Массив массивов с ключами height, width, length, weight, quantity */
        public readonly ?array $packages = null,
        /** @var array[]|null $composite Массив массивов с ключами product_id, quantity */
        public readonly ?array $composite = null,
        /** @var string[]|null $colors */
        public readonly ?array $colors = null,
        public readonly int $packs = 1,
    )
    {
    }
}
