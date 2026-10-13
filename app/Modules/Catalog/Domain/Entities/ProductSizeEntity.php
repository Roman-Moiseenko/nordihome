<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Entities;

/**
 * Конкретный размер товара — один вариант (комбинация осей размерной сетки).
 */
final class ProductSizeEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public int $productId {
        get => $this->productId;
        set => $this->productId = $value;
    }

    /**
     * Значения осей, например ['size' => '30', 'width' => 'D'].
     *
     * @var array<string, string>
     */
    public array $values {
        get => $this->values;
        set => $this->values = $value;
    }

    public bool $isActive = true {
        get => $this->isActive;
        set => $this->isActive = $value;
    }

    /**
     * @param array<string, string> $values
     */
    public function __construct(
        int $productId,
        array $values,
        bool $isActive = true,
    ) {
        $this->productId = $productId;
        $this->values = $values;
        $this->isActive = $isActive;
    }

    /**
     * Канонический ключ варианта, не зависящий от порядка осей.
     */
    public function key(): string
    {
        return self::keyFromValues($this->values);
    }

    /**
     * @param array<string, string> $values
     */
    public static function keyFromValues(array $values): string
    {
        ksort($values);

        $parts = [];
        foreach ($values as $code => $value) {
            $parts[] = $code . '=' . $value;
        }

        return implode('|', $parts);
    }
}
