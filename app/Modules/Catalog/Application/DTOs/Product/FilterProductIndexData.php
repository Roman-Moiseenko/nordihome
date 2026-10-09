<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product;

use Spatie\LaravelData\Data;

/**
 * Фильтр и данные страницы списка товаров (Catalog/Product/Index).
 *
 * Поля фильтра: name, room, show.
 * $count — число применённых фильтров (мутабельное, пишет репозиторий).
 * $all, $active, $draft, $notSale, $delete — счётчики товаров по состояниям
 * (мутабельные, заполняются репозиторием одним агрегирующим запросом).
 */
class FilterProductIndexData extends Data
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?int    $room = null,
        public readonly ?string $show = null,
        public readonly int     $perPage = 20,
        public ?int             $count = 0,
        public ?int             $all = 0,
        public ?int             $active = 0,
        public ?int             $draft = 0,
        public ?int             $notSale = 0,
        public ?int             $delete = 0,
    ) {}
}
