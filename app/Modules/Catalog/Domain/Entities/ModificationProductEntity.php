<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Entities;

/**
 * Связь товара с модификацией.
 *
 * Соответствует строке в modifications_products.
 * Identity внутри агрегата Modification — по product_id.
 */
final class ModificationProductEntity
{
    public int $productId {
        get => $this->productId;
        set => $this->productId = $value;
    }

    /**
     * @var array<int, int> attribute_id => variant_id
     */
    public array $values {
        get => $this->values;
        set => $this->values = $value;
    }

    public bool $isPrimary = false {
        get => $this->isPrimary;
        set => $this->isPrimary = $value;
    }

    /**
     * @param array<int, int> $values attribute_id => variant_id
     */
    public function __construct(
        int $productId,
        array $values,
        bool $isPrimary = false,
    ) {
        $this->productId = $productId;
        $this->values = $values;
        $this->isPrimary = $isPrimary;
    }

    public function makePrimary(): void
    {
        $this->isPrimary = true;
    }

    public function demote(): void
    {
        $this->isPrimary = false;
    }
}
