<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product;

use Spatie\LaravelData\Data;

/**
 * DTO для списка товаров в комнате (на странице Show комнаты).
 * Содержит только то, что нужно для отображения в таблице товаров комнаты.
 */
class ProductRoomData extends Data
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $code,
        public readonly string  $name,
        public readonly ?string $image,
        public readonly bool    $published,
        public readonly bool    $not_sale,
    )
    {
    }
}
