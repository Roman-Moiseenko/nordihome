<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Entities;

/**
 * Aggregate root «Модификация товара».
 *
 * Сознательно НЕ содержит base_product_id — роль базового товара
 * хранится на связи (modifications_products.is_primary).
 */
final class ModificationEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    /**
     * Оси модификации — ID атрибутов-вариантов.
     *
     * @var int[]
     */
    public array $attributes {
        get => $this->attributes;
        set => $this->attributes = $value;
    }

    /**
     * Связи «товар → значения». Ключ — product_id.
     *
     * @var array<int, ModificationProductEntity>
     */
    public array $products = [] {
        &get => $this->products;
    }

    /**
     * @param int[] $attributes
     * @param ModificationProductEntity[] $products
     */
    public function __construct(
        string $name,
        array $attributes,
        array $products = [],
    ) {
        $this->name = $name;
        $this->attributes = $attributes;

        foreach ($products as $product) {
            $this->products[$product->productId] = $product;
        }
    }

    /**
     * @param int[] $attributes
     */
    public static function create(string $name, array $attributes): self
    {
        return new self(name: $name, attributes: $attributes);
    }

    public function rename(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param array<int, int> $values attribute_id => variant_id
     */
    public function addProduct(int $productId, array $values, bool $primary = false): ModificationProductEntity
    {
        if (isset($this->products[$productId])) {
            throw new \DomainException("Product {$productId} is already in modification");
        }

        if ($primary) {
            $this->demotePrimary();
        }

        // первый товар всегда primary
        if ($this->products === []) {
            $primary = true;
        }

        $product = new ModificationProductEntity(
            productId: $productId,
            values: $values,
            isPrimary: $primary,
        );

        $this->products[$productId] = $product;

        return $product;
    }

    public function removeProduct(int $productId): void
    {
        if (!isset($this->products[$productId])) {
            throw new \DomainException("Product {$productId} is not in modification");
        }

        $wasPrimary = $this->products[$productId]->isPrimary;
        unset($this->products[$productId]);

        // если удалили primary — назначаем первого из оставшихся
        if ($wasPrimary && $this->products !== []) {
            $firstId = array_key_first($this->products);
            $this->products[$firstId]->makePrimary();
        }
    }

    public function setPrimary(int $productId): void
    {
        if (!isset($this->products[$productId])) {
            throw new \DomainException("Product {$productId} is not in modification");
        }

        $this->demotePrimary();
        $this->products[$productId]->makePrimary();
    }

    public function primaryProductId(): ?int
    {
        foreach ($this->products as $product) {
            if ($product->isPrimary) {
                return $product->productId;
            }
        }

        return null;
    }

    /**
     * Есть ли уже товар с точно таким же набором значений вариантов
     * (защита от дублирования комбинаций для 1, 2 и 3 осей).
     *
     * @param array<int, int> $values attribute_id => variant_id
     */
    public function hasProductWithValues(array $values): bool
    {
        foreach ($this->products as $product) {
            if ($product->values == $values) {
                return true;
            }
        }

        return false;
    }

    public function withId(int $id): self
    {
        $clone = clone $this;
        $clone->id = $id;

        return $clone;
    }

    private function demotePrimary(): void
    {
        foreach ($this->products as $product) {
            $product->demote();
        }
    }
}
