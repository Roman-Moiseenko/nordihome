<?php

declare(strict_types=1);

namespace App\Modules\Output\Application\DTOs\ECommerce;

use Spatie\LaravelData\Data;

/**
 * DTO ответа e-commerce слоя (Google Analytics).
 *
 * type — динамический ключ события (purchase, add, checkout и т.д.),
 * items — список товаров, участвующих в событии.
 */
class ECommerceData extends Data
{
    public function __construct(
        public readonly string $currencyCode,
        public readonly string $type,
        /** @var ECommerceItemData[] */
        public readonly array $items,
    ) {}
}
