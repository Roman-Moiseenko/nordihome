<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\DTOs\Parts;

class ProductCard
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $code,
        public readonly float $price,
        public readonly float $rating,
        public readonly string $brand,
        public readonly string $image,
        public readonly bool $priority,
        public readonly bool $is_new,
        public readonly bool $reduced,
        public readonly bool $only_on_order,
    )
    {
    }
}
