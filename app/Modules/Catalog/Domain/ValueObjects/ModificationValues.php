<?php

namespace App\Modules\Catalog\Domain\ValueObjects;

use JsonSerializable;

final readonly class ModificationValues implements JsonSerializable
{
    /** @var array<int, int> */
    private array $map;

    /**
     * @param array<int|string, int|string> $map
     */
    public function __construct(array $map)
    {
        $clean = [];

        foreach ($map as $attributeId => $variantId) {
            $clean[(int) $attributeId] = (int) $variantId;
        }

        $this->map = $clean;
    }

    public static function fromArray(array $map): self
    {
        return new self($map);
    }

    public static function empty(): self
    {
        return new self([]);
    }

    /**
     * @return array<int, int>
     */
    public function toArray(): array
    {
        return $this->map;
    }

    public function forAttribute(int $attributeId): ?int
    {
        return $this->map[$attributeId] ?? null;
    }

    public function has(int $attributeId): bool
    {
        return isset($this->map[$attributeId]);
    }

    public function isEmpty(): bool
    {
        return $this->map === [];
    }

    /**
     * Полностью ли покрыты переданные оси.
     *
     * @param int[] $attributeIds
     */
    public function covers(array $attributeIds): bool
    {
        foreach ($attributeIds as $id) {
            if (!isset($this->map[$id])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<int, int>
     */
    public function jsonSerialize(): array
    {
        return $this->map;
    }

    public function equals(self $other): bool
    {
        return $this->hash() === $other->hash();
    }

    /**
     * Стабильный ключ без учёта порядка — для поиска в матрице.
     */
    public function hash(): string
    {
        $map = $this->map;
        ksort($map);

        $parts = [];
        foreach ($map as $attrId => $variantId) {
            $parts[] = "{$attrId}={$variantId}";
        }

        return implode(':', $parts);
    }
}
