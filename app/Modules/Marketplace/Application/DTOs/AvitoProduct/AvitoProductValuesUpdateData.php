<?php

namespace App\Modules\Marketplace\Application\DTOs\AvitoProduct;

use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Data;

/**
 * Обновление значений параметров категории (вкладка «Параметры»).
 */
class AvitoProductValuesUpdateData extends Data
{
    public function __construct(
        /** @var array<int, array{avito_category_field_id?: int, field_id?: int, value?: mixed}> */
        #[ArrayType]
        public readonly array $values = [],
    ) {}
}
