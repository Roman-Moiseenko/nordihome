<?php

namespace App\Modules\Catalog\Domain\ValueObjects;

use App\Modules\Catalog\Domain\Exceptions\DuplicateModificationAttributesException;
use App\Modules\Catalog\Domain\Exceptions\EmptyModificationAttributesException;
use App\Modules\Catalog\Domain\Exceptions\TooManyModificationAttributesException;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * Коллекция ID атрибутов, задающих оси модификации.
 *
 * Инварианты:
 *  - минимум 1 атрибут;
 *  - максимум MAX атрибутов;
 *  - без дубликатов;
 *  - порядок сохраняется — важен для UI-матрицы.
 *
 * Сознательно generic (не Pair/Triple), чтобы лимит менялся одной константой.
 */
final readonly class ModificationAttributes implements
    Countable,
    IteratorAggregate,
    JsonSerializable
{
    /**
     * Жёсткий лимит осей модификации.
     *
     * Мировая практика: 2 — оптимум, 3 — максимум.
     * Меняется здесь — Request-валидация и UI читают ту же константу.
     */
    public const MAX = 3;

    /** @var int[] */
    private array $ids;

    /**
     * @param int[] $ids
     */
    public function __construct(array $ids)
    {
        $normalized = $this->normalize($ids);

        if ($normalized === []) {
            throw new EmptyModificationAttributesException();
        }

        if (count($normalized) > self::MAX) {
            throw new TooManyModificationAttributesException(self::MAX);
        }

        $this->ids = $normalized;
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    private function normalize(array $ids): array
    {
        $seen   = [];
        $result = [];

        foreach ($ids as $id) {
            $id = (int) $id;

            if (isset($seen[$id])) {
                throw new DuplicateModificationAttributesException($id);
            }

            $seen[$id] = true;
            $result[]  = $id;
        }

        return $result;
    }

    /**
     * @return int[]
     */
    public function ids(): array
    {
        return $this->ids;
    }

    public function first(): int
    {
        return $this->ids[0];
    }

    public function second(): ?int
    {
        return $this->ids[1] ?? null;
    }

    public function contains(int $attributeId): bool
    {
        return in_array($attributeId, $this->ids, strict: true);
    }

    public function count(): int
    {
        return count($this->ids);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->ids);
    }

    /**
     * @return int[]
     */
    public function jsonSerialize(): array
    {
        return $this->ids;
    }

    public function equals(self $other): bool
    {
        return $this->ids === $other->ids;
    }
}

