<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Entities;

use App\Modules\Catalog\Domain\ValueObjects\SizeGridDimension;

/**
 * Размерная сетка товара ('Обувь взрослая', 'Одежда мужская').
 */
final class SizeGridEntity
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
     * Измерения сетки: 'size' → 'Размер', 'width' → 'Полнота'.
     *
     * @var SizeGridDimension[]
     */
    public array $dimensions = [] {
        get => $this->dimensions;
        set => $this->dimensions = $value;
    }

    /**
     * Все значения сетки (по всем измерениям).
     *
     * @var SizeGridValueEntity[]
     */
    public array $values = [] {
        get => $this->values;
        set => $this->values = $value;
    }

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Все значения конкретного измерения, например все размеры 'size'.
     *
     * @return SizeGridValueEntity[]
     */
    public function getValuesByDimension(string $dimensionCode): array
    {
        return array_values(array_filter(
            $this->values,
            fn(SizeGridValueEntity $v) => $v->getDimensionCode() === $dimensionCode,
        ));
    }

    /**
     * Добавить значение сетки.
     */
    public function addValue(SizeGridValueEntity $value): void
    {
        $this->values = [...$this->values, $value];
    }

    /**
     * Удалить значение сетки по id (если id ещё не назначен — по объекту).
     */
    public function removeValue(SizeGridValueEntity $value): void
    {
        $this->values = array_values(array_filter(
            $this->values,
            fn(SizeGridValueEntity $v) => $v !== $value,
        ));
    }

    /**
     * Удалить значение сетки по id.
     */
    public function removeValueById(int $id): void
    {
        $this->values = array_values(array_filter(
            $this->values,
            fn(SizeGridValueEntity $v) => $v->id !== $id,
        ));
    }

    /**
     * Проверить, что измерение с указанным кодом присутствует в сетке.
     */
    public function hasDimension(string $dimensionCode): bool
    {
        foreach ($this->dimensions as $dimension) {
            if ($dimension->code === $dimensionCode) {
                return true;
            }
        }

        return false;
    }

    /**
     * Все возможные варианты размеров — декартово произведение активных значений
     * по осям сетки (в порядке следования измерений).
     *
     * @return array<int, array<string, string>> Каждый вариант: ['size' => '30', 'width' => 'D']
     */
    public function getCombinations(): array
    {
        $codes = array_map(fn(SizeGridDimension $d) => $d->code, $this->dimensions);

        if ($codes === []) {
            return [];
        }

        $axes = [];
        foreach ($codes as $code) {
            $values = array_values(array_filter(
                $this->getValuesByDimension($code),
                fn(SizeGridValueEntity $v) => $v->isActive,
            ));

            usort(
                $values,
                fn(SizeGridValueEntity $a, SizeGridValueEntity $b) =>
                    [$a->sortOrder, $a->id ?? 0] <=> [$b->sortOrder, $b->id ?? 0],
            );

            if ($values === []) {
                return [];
            }

            $axes[$code] = array_map(
                fn(SizeGridValueEntity $v) => $v->value,
                $values,
            );
        }

        $combinations = [[]];

        foreach ($codes as $code) {
            $next = [];

            foreach ($combinations as $combo) {
                foreach ($axes[$code] as $value) {
                    $next[] = [...$combo, $code => $value];
                }
            }

            $combinations = $next;
        }

        return $combinations;
    }
}
