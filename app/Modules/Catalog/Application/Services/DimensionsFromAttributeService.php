<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\Services;

use App\Modules\Base\Entity\Dimensions;
use App\Modules\Catalog\Infrastructure\Models\Product;

class DimensionsFromAttributeService
{
    /**
     * Извлекает габариты из сырых данных атрибутов и присваивает их товару.
     *
     * @param int         $productId    ID товара
     * @param array|null  $array_height Массив значений высоты (например ["76"])
     * @param array|null  $array_depth  Массив значений глубины/длины (например ["120-180"])
     * @param array|null  $array_width  Массив значений ширины (например ["80"])
     */
    public function execute(int $productId, ?array $array_height, ?array $array_depth, ?array $array_width): void
    {
        /** @var Product|null $product */
        $product = Product::find($productId);
        if ($product === null) {
            return;
        }

        // Извлекаем и парсим значения
        $height = $this->parseDimensionValue($array_height);
        $depth  = $this->parseDimensionValue($array_depth);
        $width  = $this->parseDimensionValue($array_width);

        // Если все три параметра null — ничего не делаем
        if ($height === null && $depth === null && $width === null) {
            return;
        }

        // Плоский товар: высота = 0
        if ($height === null) {
            $height = 0.0;
        }

        // Определяем тип габаритов
        if ($depth === null || $width === null) {
            // Круглый товар (один из параметров отсутствует)
            $type = Dimensions::TYPE_DIAMETER;
            // Для диаметра: отсутствующий параметр берём из имеющегося
            if ($depth === null && $width !== null) {
                $depth = $width;
            } elseif ($width === null && $depth !== null) {
                $width = $depth;
            }
        } else {
            $type = Dimensions::TYPE_LENGTH;
        }

        $product->dimensions = Dimensions::create(
            width: $width ?? 0.0,
            height: $height,
            depth: $depth ?? 0.0,
            weight: 0.0,
            measure: Dimensions::MEASURE_KG,
            type: $type,
        );
        $product->save();
    }

    /**
     * Извлекает значения из массива, парсит их и возвращает минимальное.
     * Поддерживает форматы:
     *   - ["76"]              → 76.0
     *   - ["120", "80"]       → 80.0 (минимальное из массива)
     *   - ["120-180"]         → 120.0 (диапазон, берётся минимальное)
     *   - ["10,5"]            → 10.5
     *   - ["120-180", "70"]   → 70.0 (диапазоны и числа вместе)
     *   - null / пустой       → null
     *
     * @param array|null $array Входной массив (например ["120-180", "70"])
     * @return float|null
     */
    private function parseDimensionValue(?array $array): ?float
    {
        if (empty($array)) {
            return null;
        }

        $numbers = [];

        foreach ($array as $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $value = trim((string)$value);

            // Диапазон вида "120-180" — разбиваем на отдельные числа
            if (str_contains($value, '-')) {
                $parts = explode('-', $value);
                foreach ($parts as $part) {
                    $num = $this->toFloat($part);
                    if ($num !== null) {
                        $numbers[] = $num;
                    }
                }
            } else {
                $num = $this->toFloat($value);
                if ($num !== null) {
                    $numbers[] = $num;
                }
            }
        }

        return !empty($numbers) ? min($numbers) : null;
    }

    /**
     * Преобразует строку в float с учётом запятой как разделителя дробной части.
     *
     * @param string $value Строковое представление числа
     * @return float|null
     */
    private function toFloat(string $value): ?float
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        // Заменяем запятую на точку для корректного приведения
        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float)$value : null;
    }
}
