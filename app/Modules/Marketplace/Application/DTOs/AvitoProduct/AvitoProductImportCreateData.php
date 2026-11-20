<?php

declare(strict_types=1);

namespace App\Modules\Marketplace\Application\DTOs\AvitoProduct;

use Spatie\LaravelData\Data;

/**
 * Данные для создания объявления Авито при загрузке из xlsx.
 *
 * Содержит основные поля товара и значения параметров категории,
 * подготовленные на этапе распознавания листа.
 */
class AvitoProductImportCreateData extends Data
{
    /**
     * @param array<int, array{avito_category_field_id: int, value: ?string}> $parameters
     */
    public function __construct(
        public readonly string $name,
        public readonly int $categoryId,
        public readonly ?string $description = null,
        public readonly ?int $sellPrice = null,
        public readonly ?string $avitoImages = null,
        public readonly ?string $tnVed = null,
        public readonly ?string $okpd2 = null,
        public readonly ?string $country = null,
        public readonly bool $delivery = false,
        public readonly ?int $productId = null,
        public readonly array $parameters = [],
    ) {}
}
