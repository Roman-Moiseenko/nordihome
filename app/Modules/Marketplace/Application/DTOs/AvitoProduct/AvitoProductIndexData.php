<?php

namespace App\Modules\Marketplace\Application\DTOs\AvitoProduct;

/**
 * Строка таблицы товаров Авито (Marketplace/Avito/Index).
 */
readonly class AvitoProductIndexData
{
    public function __construct(
        public int $id,
        public ?string $avitoId,
        public string $name,
        public ?string $code,
        public string $categoryPath,
        public bool $delivery,
        public bool $active,
        public ?string $tnVed,
    ) {}
}
