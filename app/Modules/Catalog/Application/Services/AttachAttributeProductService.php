<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\Services;

use App\Modules\Catalog\Infrastructure\Models\Attribute;
use App\Modules\Catalog\Infrastructure\Models\AttributeVariant;
use App\Modules\Catalog\Infrastructure\Models\Product;

class AttachAttributeProductService
{
    /**
     * Назначает товару атрибут "Цвет" с указанными вариантами.
     * Если вариант цвета не существует — создаёт его.
     * Если атрибут уже привязан к товару — добавляет новые варианты к существующим.
     *
     * @param int   $productId ID товара
     * @param array $colors    Список необработанных названий цветов (например ["белый", "черный"])
     */
    public function SetColorAttribute(int $productId, array $colors): void
    {
        /** @var Product|null $product */
        $product = Product::find($productId);
        if ($product === null) return;


        /** @var Attribute|null $attribute */
        $attribute = Attribute::where('name', 'Цвет')->first();
        if ($attribute === null) {
            return;
        }

        foreach ($colors as $color) {
            // Нормализация: первая буква заглавная, остальные строчные (русские)
            $colorName = $this->normalizeVariantName($color);
            if (empty($colorName)) {
                continue;
            }

            // Ищем или создаём вариант цвета
            $variant = $this->findOrCreateVariant($attribute, $colorName);

            // Добавляем вариант товару (с учётом уже существующих)
            $this->attachVariantToProduct($product, $attribute, $variant);
        }
    }

    /**
     * Назначает товару атрибут "Материал" с указанными вариантами.
     * Если вариант материала не существует — создаёт его.
     * Если атрибут уже привязан к товару — добавляет новые варианты к существующим.
     *
     * @param int   $productId ID товара
     * @param array $materials Список необработанных названий материалов (например ["дерево", "металл"])
     */
    public function SetMaterialAttribute(int $productId, array $materials): void
    {
        /** @var Product|null $product */
        $product = Product::find($productId);
        if ($product === null) {
            return;
        }

        /** @var Attribute|null $attribute */
        $attribute = Attribute::where('name', 'Материал')->first();
        if ($attribute === null) {
            return;
        }

        foreach ($materials as $material) {
            // Нормализация: первая буква заглавная, остальные строчные (русские)
            $materialName = $this->normalizeVariantName($material);
            if (empty($materialName)) {
                continue;
            }

            // Ищем или создаём вариант материала
            $variant = $this->findOrCreateVariant($attribute, $materialName);

            // Добавляем вариант товару (с учётом уже существующих)
            $this->attachVariantToProduct($product, $attribute, $variant);
        }
    }

    /**
     * Приводит название цвета к формату: первая буква заглавная, остальные строчные.
     *
     * @param string $color Исходное название цвета
     * @return string Нормализованное название
     */
    private function normalizeVariantName(string $color): string
    {
        $color = trim($color);
        if (empty($color)) {
            return '';
        }

        $first = mb_strtoupper(mb_substr($color, 0, 1, 'UTF-8'), 'UTF-8');
        $rest  = mb_strtolower(mb_substr($color, 1, null, 'UTF-8'), 'UTF-8');

        return $first . $rest;
    }

    /**
     * Ищет существующий вариант цвета по имени или создаёт новый.
     *
     * @param Attribute $attribute Атрибут "Цвет"
     * @param string    $name      Нормализованное название цвета
     * @return AttributeVariant
     */
    private function findOrCreateVariant(Attribute $attribute, string $name): AttributeVariant
    {
        // Ищем среди уже загруженных вариантов
        foreach ($attribute->variants as $variant) {
            if ($variant->name === $name) {
                return $variant;
            }
        }

        // Если не найден — запрос в БД
        $variant = $attribute->variants()->where('name', $name)->first();
        if ($variant !== null) {
            return $variant;
        }

        // Создаём новый вариант
        return $attribute->addVariant($name);
    }

    /**
     * Привязывает вариант цвета к товару.
     * Если атрибут уже связан с товаром, добавляет вариант к существующим.
     *
     * @param Product          $product   Товар
     * @param Attribute        $attribute Атрибут "Цвет"
     * @param AttributeVariant $variant   Вариант цвета
     */
    private function attachVariantToProduct(Product $product, Attribute $attribute, AttributeVariant $variant): void
    {
        // Проверяем, существует ли уже связь товара с этим атрибутом
        $existing = $product->prod_attributes()->where('attribute_id', $attribute->id)->first();

        if ($existing !== null) {
            // Декодируем текущее значение (массив variant_id)
            $currentValue = json_decode($existing->pivot->value, true) ?? [];
            if (!is_array($currentValue)) {
                $currentValue = [$currentValue];
            }

            // Если такой вариант уже назначен — пропускаем
            if (in_array($variant->id, $currentValue, true)) {
                return;
            }

            $merged = array_merge($currentValue, [$variant->id]);
            $product->prod_attributes()->updateExistingPivot(
                $attribute->id,
                ['value' => json_encode(array_values($merged))]
            );
        } else {
            // Создаём новую связь
            $product->prod_attributes()->attach(
                $attribute->id,
                ['value' => json_encode([$variant->id])]
            );
        }
    }
}
