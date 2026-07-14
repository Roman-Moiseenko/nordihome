<?php

namespace App\Modules\Content\Application\DTOs\Widget;

use Spatie\LaravelData\Data;

/**
 * DTO для возврата виджетов, сгруппированных по категориям.
 */
class WidgetListByCategoryData extends Data
{
    public function __construct(
        public readonly string $key,
        public readonly string $label,
        /** @var WidgetIndexData[] */
        public array $widgets = [],
    ) {}
}
