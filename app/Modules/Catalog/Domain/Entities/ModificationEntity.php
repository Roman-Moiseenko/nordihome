<?php

namespace App\Modules\Catalog\Domain\Entities;
use App\Modules\Catalog\Domain\ValueObjects\ModificationAttributes;
use App\Modules\Catalog\Domain\ValueObjects\ModificationName;

/**
 * Aggregate root «Модификация товара».
 *
 * Сознательно НЕ содержит:
 *  - base_product_id    — роль на pivot (modifications_products.is_primary);
 *  - список товаров     — связи, загружаются репозиторием;
 *  - ModificationValues — принадлежат связям, не агрегату.
 *
 * Держит только:
 *  - идентичность (id, private(set));
 *  - название (VO ModificationName);
 *  - оси модификации (VO ModificationAttributes).
 *
 * PHP 8.4 property hooks:
 *  - `private(set)` — id доступен на чтение всем, на запись — только внутри;
 *  - `set`-хуки на name/attributes принимают и raw-значение, и готовый VO,
 *    нормализуя первое во второе. Никаких ручных сеттеров.
 */
final class ModificationEntity
{
    /**
     * Идентичность. Присваивается только внутри класса
     * (конструктор / withId). Снаружи — read-only.
     */
    public private(set) ?int $id = null;

    /**
     * Название модификации.
     *
     * Set-хук принимает string или ModificationName.
     * Валидация — на стороне VO (пустота, длина).
     */
    public ModificationName $name {
        set (ModificationName|string $value) {
            $this->name = $value instanceof ModificationName
                ? $value
                : new ModificationName($value);
        }
    }

    /**
     * Оси модификации.
     *
     * Set-хук принимает array<int> или ModificationAttributes.
     * Валидация — на стороне VO (1..MAX, без дубликатов).
     */
    public ModificationAttributes $attributes {
        set (ModificationAttributes|array $value) {
            $this->attributes = $value instanceof ModificationAttributes
                ? $value
                : new ModificationAttributes($value);
        }
    }

    /**
     * @param ModificationName|string       $name
     * @param ModificationAttributes|array  $attributes
     */
    public function __construct(
        ?int $id,
        ModificationName|string $name,
        ModificationAttributes|array $attributes,
    ) {
        $this->id         = $id;
        $this->name       = $name;       // ← triggers set hook
        $this->attributes = $attributes; // ← triggers set hook
    }

    /**
     * Фабрика для новой модификации (id ещё нет).
     */
    public static function create(
        ModificationName|string $name,
        ModificationAttributes|array $attributes,
    ): self {
        return new self(null, $name, $attributes);
    }

    public function isNew(): bool
    {
        return $this->id === null;
    }

    /**
     * Переименование. Валидация — в VO ModificationName.
     */
    public function rename(ModificationName|string $newName): void
    {
        $this->name = $newName; // ← triggers set hook
    }

    /**
     * Смена осей модификации.
     *
     * Domain позволяет, но Application обязан проверить,
     * что все товары внутри всё ещё покрывают новые оси
     * (или что модификация пустая). Эта проверка — на уровне Action,
     * потому что требует доступа к товарам.
     */
    public function changeAttributes(ModificationAttributes|array $newAttributes): void
    {
        $this->attributes = $newAttributes; // ← triggers set hook
    }

    /**
     * Возвращает копию с проставленным id.
     * Используется репозиторием после INSERT.
     *
     * Присваивание id работает, потому что мы внутри класса
     * (private(set) разрешает запись только изнутри).
     */
    public function withId(int $id): self
    {
        $clone     = clone $this;
        $clone->id = $id;

        return $clone;
    }
}
