<?php

declare(strict_types=1);

namespace App\Modules\Output\Application\DTOs\ECommerce;

use Spatie\LaravelData\Data;

/**
 * DTO товара для передачи в e-commerce слой (Google Analytics).
 */
class ECommerceItemData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly float $price,
        public readonly string $brand,
        public readonly string $category,
        public readonly int $quantity,
    ) {}
}
