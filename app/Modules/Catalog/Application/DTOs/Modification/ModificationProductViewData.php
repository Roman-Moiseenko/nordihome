<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Modification;

use Spatie\LaravelData\Data;

/**
 * Товар внутри модификации (Catalog/Modification/Show).
 *
 * Данные берутся как из pivot modifications_products (id, is_primary),
 * так и из самого товара products (name, code, image).
 */
class ModificationProductViewData extends Data
{
    /**
     * @param string[] $values Названия выбранных вариантов (в порядке осей модификации).
     */
    public function __construct(
        /** id связи modifications_products (для будущего удаления товара из модификации). */
        public readonly int $id,
        /** id товара (для ссылки на карточку товара). */
        public readonly int $productId,
        public readonly string $name,
        public readonly string $code,
        public readonly string $image,
        public readonly array $values = [],
        public readonly bool $isPrimary = false,
    ) {
    }
}
