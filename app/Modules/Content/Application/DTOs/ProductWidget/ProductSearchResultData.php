<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\DTOs\ProductWidget;

use Spatie\LaravelData\Data;

class ProductSearchResultData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $code,
        public readonly string $code_search,
        public readonly ?string $url,
        public readonly ?string $short,
        public readonly ?float $price,
        public readonly ?int $quantity,
        public readonly bool $in_stock,
        public readonly ?string $image_src,
        public readonly ?string $image_alt,
        public readonly ?string $image_next_src,
        public readonly ?string $image_next_alt,
    ) {}
}
