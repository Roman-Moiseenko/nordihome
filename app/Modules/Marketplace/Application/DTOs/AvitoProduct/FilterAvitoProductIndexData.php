<?php

namespace App\Modules\Marketplace\Application\DTOs\AvitoProduct;

use Spatie\LaravelData\Data;

/**
 * Фильтр списка товаров Авито (Marketplace/Avito/Index).
 *
 * $show     — null|'active' (по умолчанию активные) | 'archive'
 * $delivery — null (все) | 'with' | 'without'
 * $count    — число применённых фильтров (мутабельное, пишет репозиторий)
 * $active   — кол-во активных объявлений (счётчики для ссылок-фильтров)
 * $archive  — кол-во объявлений в архиве
 */
class FilterAvitoProductIndexData extends Data
{
    public function __construct(
        public readonly ?string $show = null,
        public readonly ?string $delivery = null,
        public readonly ?string $search = null,
        public readonly int $perPage = 20,
        public ?int $count = 0,
        public ?int $active = 0,
        public ?int $archive = 0,
    ) {}
}
