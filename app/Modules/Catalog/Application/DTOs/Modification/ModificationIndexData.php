<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Modification;

use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;

/**
 * DTO строки списка модификаций (Catalog/Modification/Index).
 *
 * Ключи сериализуются в snake_case, чтобы соответствовать контракту
 * фронтенда (Index.vue). Изображение primary-товара загружается на
 * фронтенде по primary_product_id (как в TableRelation.vue).
 */
class ModificationIndexData extends Data
{
    /**
     * @param string[] $nameAttributes
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $quantity,
        #[MapOutputName('primary_product_id')]
        public readonly ?int $primaryProductId = null,
        #[MapOutputName('name_attributes')]
        public readonly array $nameAttributes = [],
    ) {
    }
}
