<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\DTOs\Menu;

use Spatie\LaravelData\Data;

class MenuData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $slug,
        public readonly string $name,
        /** @var MenuItemData[] */
        public readonly array $items,
    )
    {
    }
}
