<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Entities;

/**
 * Значение размерной сетки внутри одного измерения.
 */
final class SizeGridValueEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    /**
     * Код измерения, к которому относится значение: 'size', 'width'.
     */
    public string $dimensionCode {
        get => $this->dimensionCode;
        set => $this->dimensionCode = $value;
    }

    /**
     * Само значение: '42', 'D', 'M', 'XL'. Уникально внутри (grid_id, dimension_code).
     */
    public string $value {
        get => $this->value;
        set => $this->value = $value;
    }

    /**
     * Отображаемое имя: '42 (EU)', 'Широкая D'. Если null — показываем value.
     */
    public ?string $label = null {
        get => $this->label;
        set => $this->label = $value;
    }

    /**
     * Порядок вывода в UI внутри измерения.
     */
    public int $sortOrder = 0 {
        get => $this->sortOrder;
        set => $this->sortOrder = $value;
    }

    /**
     * Показывать значение в UI. false — скрыто, но не удалено.
     */
    public bool $isActive = true {
        get => $this->isActive;
        set => $this->isActive = $value;
    }

    public function __construct(
        string $dimensionCode,
        string $value,
        ?string $label = null,
        int $sortOrder = 0,
        bool $isActive = true,
    ) {
        $this->dimensionCode = $dimensionCode;
        $this->value = $value;
        $this->label = $label;
        $this->sortOrder = $sortOrder;
        $this->isActive = $isActive;
    }

    public function getDimensionCode(): string
    {
        return $this->dimensionCode;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * Значение для отображения в UI.
     */
    public function displayValue(): string
    {
        return $this->label !== null && $this->label !== ''
            ? $this->label
            : $this->value;
    }
}
